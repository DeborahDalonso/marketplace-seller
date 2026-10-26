<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Pedidos recebidos. O orders filtra pelo sellerId do token, mas o painel
 * manda o filtro de qualquer jeito: admin também entra aqui e, sem ele,
 * veria os pedidos de todo mundo.
 */
class OrderService
{
    private const PER_PAGE = 20;

    public function __construct(
        private readonly OrdersClient $orders,
        private readonly SellerSession $session,
    ) {}

    public function list(int $page): LengthAwarePaginator
    {
        $orders = $this->orders->orders([
            'sellerId' => $this->session->sellerId(),
            'page' => $page,
            'perPage' => self::PER_PAGE,
        ]);

        return $orders->through(fn (array $order) => $this->withSellerShare($order));
    }

    public function find(string $id): array
    {
        return $this->withSellerShare($this->orders->order($id));
    }

    /**
     * Um pedido pode ter itens de vários vendedores. O total do pedido é do
     * comprador; o que interessa aqui é quanto desse total é desta loja.
     */
    private function withSellerShare(array $order): array
    {
        $mine = array_filter(
            $order['items'] ?? [],
            fn (array $item) => ($item['sellerId'] ?? null) === $this->session->sellerId(),
        );

        $order['sellerItems'] = count($mine);
        $order['sellerCents'] = array_sum(array_map(
            fn (array $item) => $item['quantity'] * $item['priceCents'],
            $mine,
        ));

        return $order;
    }
}
