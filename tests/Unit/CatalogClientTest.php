<?php

namespace Tests\Unit;

use App\Exceptions\RemoteApiException;
use App\Services\CatalogClient;
use App\Services\SellerSession;
use Illuminate\Http\Client\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CatalogClientTest extends TestCase
{
    private function client(): CatalogClient
    {
        return new CatalogClient('http://catalog.test', app(SellerSession::class));
    }

    private function expectRemoteError(string $code, int $status, callable $call): void
    {
        try {
            $call();
            $this->fail('esperava RemoteApiException');
        } catch (RemoteApiException $e) {
            $this->assertSame($code, $e->errorCode);
            $this->assertSame($status, $e->status);
            $this->assertSame(__('errors.'.$code), $e->getMessage());
        }
    }

    public function test_abre_o_envelope_e_devolve_so_o_data(): void
    {
        Http::fake(['http://catalog.test/products/prd_01' => $this->envelope(['id' => 'prd_01', 'name' => 'Fone'])]);

        $this->assertSame(['id' => 'prd_01', 'name' => 'Fone'], $this->client()->product('prd_01'));
    }

    public function test_envia_o_token_da_sessao_e_aceita_json(): void
    {
        session(['seller.token' => 'tok_xyz']);
        Http::fake(['http://catalog.test/categories' => $this->envelope([])]);

        $this->client()->categories();

        Http::assertSent(fn (Request $request) => $request->hasHeader('Authorization', 'Bearer tok_xyz')
            && $request->hasHeader('Accept', 'application/json'));
    }

    public function test_sem_token_na_sessao_nao_manda_authorization(): void
    {
        Http::fake(['http://catalog.test/categories' => $this->envelope([])]);

        $this->client()->categories();

        Http::assertSent(fn (Request $request) => ! $request->hasHeader('Authorization'));
    }

    public function test_pagina_do_contrato_vira_paginator(): void
    {
        Http::fake(['http://catalog.test/products*' => $this->envelope([
            'items' => [['id' => 'prd_01'], ['id' => 'prd_02']], 'page' => 2, 'perPage' => 2, 'total' => 5,
        ])]);

        $page = $this->client()->products(['page' => 2]);

        $this->assertInstanceOf(LengthAwarePaginator::class, $page);
        $this->assertSame(5, $page->total());
        $this->assertSame(2, $page->currentPage());
        $this->assertSame(3, $page->lastPage());
        $this->assertCount(2, $page->items());
    }

    public function test_categorias_aceitam_lista_direta_ou_dentro_de_items(): void
    {
        Http::fake(['http://catalog.test/categories' => $this->envelope(['items' => [['id' => 'cat_01']]])]);

        $this->assertSame([['id' => 'cat_01']], $this->client()->categories());
    }

    public function test_404_vira_not_found(): void
    {
        Http::fake(['http://catalog.test/products/prd_x' => $this->failure('Produto não encontrado', 404)]);

        $this->expectRemoteError('remote.not_found', 404, fn () => $this->client()->product('prd_x'));
    }

    public function test_401_vira_sessao_expirada(): void
    {
        Http::fake(['http://catalog.test/products/prd_x' => $this->failure('Token inválido', 401)]);

        $this->expectRemoteError('auth.expired', 401, fn () => $this->client()->product('prd_x'));
    }

    public function test_422_carrega_os_erros_por_campo(): void
    {
        Http::fake(['http://catalog.test/products' => $this->failure('Dados inválidos', 422, [
            ['field' => 'name', 'message' => 'obrigatório'],
        ])]);

        try {
            $this->client()->createProduct([]);
            $this->fail('esperava RemoteApiException');
        } catch (RemoteApiException $e) {
            $this->assertSame('remote.validation', $e->errorCode);
            $this->assertSame([['field' => 'name', 'message' => 'obrigatório']], $e->errors);
        }
    }

    public function test_500_vira_catalog_indisponivel_sem_vazar_o_corpo(): void
    {
        Http::fake(['http://catalog.test/products' => Http::response('<html>stack trace</html>', 500)]);

        $this->expectRemoteError('catalog.unavailable', 503, fn () => $this->client()->createProduct(['name' => 'x']));
    }

    public function test_success_false_com_http_200_tambem_e_erro(): void
    {
        Http::fake(['http://catalog.test/products/prd_x' => Http::response(['success' => false, 'message' => 'ops'], 200)]);

        $this->expectRemoteError('catalog.unavailable', 503, fn () => $this->client()->product('prd_x'));
    }

    public function test_conexao_recusada_vira_catalog_indisponivel(): void
    {
        Http::fake(['http://catalog.test/*' => Http::failedConnection()]);

        $this->expectRemoteError('catalog.unavailable', 503, fn () => $this->client()->categories());
    }
}
