<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;

class OrdersClient extends RemoteClient
{
    protected function service(): string
    {
        return 'orders';
    }

    public function orders(array $query): LengthAwarePaginator
    {
        return $this->paginate($this->send('get', '/orders', $query));
    }

    public function order(string $id): array
    {
        return $this->send('get', "/orders/{$id}");
    }
}
