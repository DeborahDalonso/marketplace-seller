<?php

namespace App\Services;

use App\Exceptions\RemoteApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Base dos clientes das APIs do ecossistema. Cada API tem uma subclasse que
 * só conhece as rotas dela; o que é igual em todas (token, timeout, envelope
 * e tradução de erro) fica aqui.
 */
abstract class RemoteClient
{
    public function __construct(
        protected readonly string $baseUrl,
        protected readonly SellerSession $session,
    ) {}

    /**
     * Nome curto da API, usado nas chaves de erro e no log. Ex.: "catalog".
     */
    abstract protected function service(): string;

    /**
     * Faz a chamada e devolve o conteúdo de "data" do envelope.
     */
    protected function send(string $method, string $path, array $payload = []): array
    {
        return $this->unwrap($this->call($method, $path, $payload), $path);
    }

    protected function call(string $method, string $path, array $payload = []): Response
    {
        try {
            return $this->request()->{$method}($path, $payload);
        } catch (ConnectionException $e) {
            Log::error("[{$this->service()}] API fora do ar", ['path' => $path, 'reason' => $e->getMessage()]);

            throw new RemoteApiException($this->service().'.unavailable', 503, previous: $e);
        }
    }

    protected function request(): PendingRequest
    {
        $pending = Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->timeout(config('services.remote.timeout_s'));

        $token = $this->session->token();

        return $token ? $pending->withToken($token) : $pending;
    }

    /**
     * Abre o envelope { success, message, data } (CONTRATOS seção 5) e
     * transforma qualquer falha em erro de domínio com mensagem própria.
     */
    protected function unwrap(Response $response, string $path): array
    {
        $body = $response->json();

        if ($response->successful() && is_array($body) && ($body['success'] ?? false)) {
            return $body['data'] ?? [];
        }

        $status = $response->status();

        Log::warning("[{$this->service()}] resposta com erro", [
            'path' => $path,
            'status' => $status,
            'message' => $body['message'] ?? null,
        ]);

        [$code, $ownStatus] = match ($status) {
            401 => ['auth.expired', 401],
            403 => ['remote.forbidden', 403],
            404 => ['remote.not_found', 404],
            409 => ['remote.conflict', 409],
            422 => ['remote.validation', 422],
            default => [$this->service().'.unavailable', 503],
        };

        throw new RemoteApiException($code, $ownStatus, $body['errors'] ?? []);
    }

    /**
     * Converte a página do contrato ({ items, page, perPage, total }) no
     * paginator do Laravel para a view usar links() como se fosse Eloquent.
     */
    protected function paginate(array $page): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            $page['items'] ?? [],
            $page['total'] ?? 0,
            $page['perPage'] ?? 20,
            $page['page'] ?? 1,
            ['path' => Paginator::resolveCurrentPath()],
        );
    }
}
