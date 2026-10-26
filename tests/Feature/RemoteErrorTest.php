<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RemoteErrorTest extends TestCase
{
    use RefreshDatabase;

    private function validProduct(): array
    {
        return ['name' => 'Fone', 'category_id' => 'cat_01', 'price' => '10', 'stock' => '1'];
    }

    public function test_api_com_erro_em_post_volta_com_flash_em_pt_br(): void
    {
        Http::fake(['http://catalog.test/products' => Http::response('Internal Server Error', 500)]);

        $this->actingAsSeller()
            ->from('/products/create')
            ->post('/products', $this->validProduct())
            ->assertRedirect('/products/create')
            ->assertSessionHas('error', __('errors.catalog.unavailable'))
            ->assertSessionHasInput('name', 'Fone');
    }

    public function test_api_fora_do_ar_em_get_mostra_pagina_de_erro_503(): void
    {
        Http::fake(['http://catalog.test/*' => Http::failedConnection()]);

        $this->actingAsSeller()
            ->get('/products')
            ->assertStatus(503)
            ->assertSee(__('errors.catalog.unavailable'))
            ->assertSee(__('errors.page.retry'))
            ->assertDontSee('Connection');
    }

    public function test_validacao_remota_volta_com_erros_por_campo(): void
    {
        Http::fake(['http://catalog.test/products' => $this->failure('Dados inválidos', 422, [
            ['field' => 'name', 'message' => 'já existe um produto com este nome'],
        ])]);

        $this->actingAsSeller()
            ->from('/products/create')
            ->post('/products', $this->validProduct())
            ->assertRedirect('/products/create')
            ->assertSessionHas('error', __('errors.remote.validation'))
            ->assertSessionHasErrors(['name' => 'já existe um produto com este nome']);
    }

    public function test_token_recusado_derruba_a_sessao_e_volta_ao_login(): void
    {
        Http::fake(['http://catalog.test/*' => $this->failure('Token expirado', 401)]);

        $this->actingAsSeller()
            ->get('/products')
            ->assertRedirect('/login')
            ->assertSessionHas('error', __('errors.auth.expired'));

        $this->assertNull(session('seller.token'));
    }

    public function test_sem_permissao_remota_mostra_403(): void
    {
        Http::fake(['http://catalog.test/products/prd_x' => $this->failure('Não é seu', 403)]);

        $this->actingAsSeller()
            ->from('/products')
            ->delete('/products/prd_x')
            ->assertRedirect('/products')
            ->assertSessionHas('error', __('errors.remote.forbidden'));
    }

    public function test_erro_remoto_em_json_responde_no_envelope(): void
    {
        Http::fake(['http://catalog.test/*' => $this->failure('Produto não encontrado', 404)]);

        $this->actingAsSeller()
            ->getJson('/products/prd_x/edit')
            ->assertStatus(404)
            ->assertExactJson([
                'success' => false,
                'message' => __('errors.remote.not_found'),
                'errors' => [],
            ]);
    }
}
