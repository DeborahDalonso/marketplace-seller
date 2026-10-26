<?php

namespace Tests\Feature;

use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private function fakeLogin(string $role = 'seller'): void
    {
        Http::fake([
            'http://auth.test/auth/login' => $this->envelope([
                'accessToken' => 'tok_abc',
                'refreshToken' => 'ref_abc',
                'user' => ['id' => self::SELLER_ID, 'email' => 'ana@example.com', 'name' => 'Ana', 'role' => $role],
            ]),
        ]);
    }

    public function test_pagina_de_login_renderiza_em_pt_br(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee(__('login.title'))
            ->assertSee(__('login.submit'));
    }

    public function test_login_valido_guarda_token_na_sessao_e_manda_criar_a_loja(): void
    {
        $this->fakeLogin();

        $response = $this->post('/login', ['email' => 'ana@example.com', 'password' => 'segredo']);

        $response->assertRedirect('/store');
        $response->assertSessionHas('info', __('store.first_time'));
        $this->assertSame('tok_abc', session('seller.token'));
        $this->assertSame(self::SELLER_ID, session('seller.user.id'));

        Http::assertSent(fn (Request $request) => $request->url() === 'http://auth.test/auth/login'
            && $request['email'] === 'ana@example.com'
            && $request['password'] === 'segredo');
    }

    public function test_login_com_loja_existente_vai_para_o_painel(): void
    {
        $this->fakeLogin();
        Store::factory()->create(['seller_id' => self::SELLER_ID]);

        $this->post('/login', ['email' => 'ana@example.com', 'password' => 'segredo'])
            ->assertRedirect('/');
    }

    public function test_login_com_credencial_invalida_mostra_erro_no_campo(): void
    {
        Http::fake(['http://auth.test/auth/login' => $this->failure('Credenciais inválidas', 401)]);

        $this->from('/login')
            ->post('/login', ['email' => 'ana@example.com', 'password' => 'errada'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email' => __('auth.failed')]);

        $this->assertNull(session('seller.token'));
    }

    public function test_login_com_role_buyer_e_bloqueado(): void
    {
        $this->fakeLogin('buyer');

        $this->from('/login')
            ->post('/login', ['email' => 'ana@example.com', 'password' => 'segredo'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email' => __('auth.role_not_allowed')]);

        $this->assertNull(session('seller.token'));
    }

    public function test_login_com_role_admin_entra(): void
    {
        $this->fakeLogin('admin');

        $this->post('/login', ['email' => 'root@example.com', 'password' => 'segredo'])
            ->assertRedirect('/store');

        $this->assertSame('tok_abc', session('seller.token'));
    }

    public function test_email_invalido_nao_chama_a_api(): void
    {
        Http::fake();

        $this->from('/login')
            ->post('/login', ['email' => 'nao-e-email', 'password' => ''])
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email', 'password']);

        Http::assertNothingSent();
    }

    public function test_logout_limpa_a_sessao(): void
    {
        $this->actingAsSeller()
            ->post('/logout')
            ->assertRedirect('/login')
            ->assertSessionHas('success', __('login.logged_out'));

        $this->assertNull(session('seller.token'));
    }

    public function test_quem_ja_entrou_nao_ve_o_formulario_de_login(): void
    {
        $this->actingAsSeller()->get('/login')->assertRedirect('/');
    }
}
