<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use RuntimeException;
use Throwable;

/**
 * Erro de domínio para qualquer falha ao falar com uma API do ecossistema.
 * O código é a chave em lang/pt_BR/errors.php; o status é o que o painel
 * responde. A causa técnica fica no log, nunca na tela.
 */
class RemoteApiException extends RuntimeException
{
    /**
     * @param  array<int, array{field: string, message: string}>  $errors
     */
    public function __construct(
        public readonly string $errorCode,
        public readonly int $status,
        public readonly array $errors = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct(__('errors.'.$errorCode), $status, $previous);
    }

    public function render(Request $request): JsonResponse|RedirectResponse|Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $this->getMessage(),
                'errors' => $this->errors,
            ], $this->status);
        }

        // Token vencido ou revogado: a sessão não serve mais, volta ao login
        if ($this->status === 401) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', $this->getMessage());
        }

        // Em GET não dá para "voltar": a página anterior pode ser a mesma que
        // falhou e viraria um laço de redirects. Mostra a mensagem no lugar.
        if ($request->isMethod('GET')) {
            return response()->view('errors.remote', ['message' => $this->getMessage()], $this->status);
        }

        return back()
            ->withInput()
            ->withErrors(collect($this->errors)->pluck('message', 'field')->all())
            ->with('error', $this->getMessage());
    }
}
