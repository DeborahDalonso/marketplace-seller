<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;

class CatalogClient extends RemoteClient
{
    protected function service(): string
    {
        return 'catalog';
    }

    public function products(array $query): LengthAwarePaginator
    {
        return $this->paginate($this->send('get', '/products', $query));
    }

    public function product(string $id): array
    {
        return $this->send('get', "/products/{$id}");
    }

    public function createProduct(array $data): array
    {
        return $this->send('post', '/products', $data);
    }

    public function updateProduct(string $id, array $data): array
    {
        return $this->send('put', "/products/{$id}", $data);
    }

    public function deleteProduct(string $id): void
    {
        $this->send('delete', "/products/{$id}");
    }

    public function categories(): array
    {
        $data = $this->send('get', '/categories');

        return $data['items'] ?? $data;
    }
}
