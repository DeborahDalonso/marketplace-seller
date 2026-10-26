<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EnsureSellerTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_sem_sessao_redireciona_para_o_login(): void
    {
        $this->get('/products')->assertRedirect('/login');
        $this->get('/orders')->assertRedirect('/login');
        $this->get('/store')->assertRedirect('/login');
        $this->get('/')->assertRedirect('/login');
    }

    public function test_sem_sessao_guarda_a_url_pedida_para_voltar_depois(): void
    {
        $this->get('/orders')->assertRedirect('/login');

        $this->assertSame(url('/orders'), session('url.intended'));
    }

    public function test_sem_sessao_em_json_responde_401_no_envelope(): void
    {
        $this->getJson('/products')
            ->assertStatus(401)
            ->assertExactJson([
                'success' => false,
                'message' => __('errors.auth.required'),
                'errors' => [],
            ]);
    }

    public function test_com_sessao_deixa_passar(): void
    {
        Http::fake();

        $this->actingAsSeller()->get('/store')->assertOk();
    }
}
