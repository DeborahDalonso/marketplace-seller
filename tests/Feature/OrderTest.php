<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private function order(array $overrides = []): array
    {
        return $overrides + [
            'id' => 'ord_01',
            'buyerId' => 'usr_buyer',
            'status' => 'paid',
            'totalCents' => 49980,
            'paymentMethod' => 'pix',
            'items' => [
                ['productId' => 'prd_01', 'sellerId' => self::SELLER_ID, 'quantity' => 2, 'priceCents' => 19990],
                ['productId' => 'prd_99', 'sellerId' => 'usr_outro', 'quantity' => 1, 'priceCents' => 10000],
            ],
            'createdAt' => '2026-10-20T13:45:00Z',
            'updatedAt' => '2026-10-20T13:50:00Z',
        ];
    }

    public function test_lista_mostra_so_a_parte_do_vendedor_no_total(): void
    {
        Http::fake(['http://orders.test/orders*' => $this->envelope([
            'items' => [$this->order(), $this->order(['id' => 'ord_02', 'status' => 'cancelled'])],
            'page' => 1,
            'perPage' => 20,
            'total' => 2,
        ])]);

        $this->actingAsSeller()
            ->get('/orders')
            ->assertOk()
            ->assertSeeInOrder(['ord_01', '20/10/2026 10:45', __('ui.status.paid'), 'Pix', 'R$ 399,80'])
            ->assertSeeInOrder(['ord_02', __('ui.status.cancelled')]);

        Http::assertSent(fn (Request $request) => str_starts_with($request->url(), 'http://orders.test/orders?')
            && $request->data()['sellerId'] === self::SELLER_ID
            && $request->hasHeader('Authorization', 'Bearer '.self::SELLER_TOKEN));
    }

    public function test_lista_vazia_mostra_estado_vazio(): void
    {
        Http::fake(['http://orders.test/orders*' => $this->envelope(['items' => [], 'page' => 1, 'perPage' => 20, 'total' => 0])]);

        $this->actingAsSeller()
            ->get('/orders')
            ->assertOk()
            ->assertSee(__('orders.empty_title'));
    }

    public function test_detalhe_separa_itens_da_loja_dos_de_outros_vendedores(): void
    {
        Http::fake(['http://orders.test/orders/ord_01' => $this->envelope($this->order())]);

        $this->actingAsSeller()
            ->get('/orders/ord_01')
            ->assertOk()
            ->assertSee('usr_buyer')
            ->assertSeeInOrder(['prd_01', __('orders.mine'), 'R$ 399,80'])
            ->assertSeeInOrder(['prd_99', __('orders.other_seller'), 'R$ 100,00'])
            ->assertSee('R$ 499,80');
    }

    public function test_pedido_inexistente_mostra_pagina_de_erro_404(): void
    {
        Http::fake(['http://orders.test/orders/ord_x' => $this->failure('Pedido não encontrado', 404)]);

        $this->actingAsSeller()
            ->get('/orders/ord_x')
            ->assertStatus(404)
            ->assertSee(__('errors.remote.not_found'));
    }
}
