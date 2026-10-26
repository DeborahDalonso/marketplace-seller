<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

/**
 * Traduz o formulário do painel para o contrato do catalog e garante que o
 * vendedor só enxerga os próprios produtos.
 */
class ProductService
{
    private const PER_PAGE = 20;

    public function __construct(
        private readonly CatalogClient $catalog,
        private readonly SellerSession $session,
    ) {}

    public function list(int $page): LengthAwarePaginator
    {
        $products = $this->catalog->products([
            'sellerId' => $this->session->sellerId(),
            'page' => $page,
            'perPage' => self::PER_PAGE,
        ]);

        // o catalog devolve só o categoryId; a lista mostra o nome
        $names = collect($this->categories())->pluck('name', 'id');

        return $products->through(fn (array $product) => $product + [
            'categoryName' => $names[$product['categoryId'] ?? ''] ?? null,
        ]);
    }

    public function find(string $id): array
    {
        return $this->catalog->product($id);
    }

    public function categories(): array
    {
        return $this->catalog->categories();
    }

    public function create(array $form): array
    {
        $product = $this->catalog->createProduct($this->toPayload($form));

        Log::info('[ProductService] produto criado', ['productId' => $product['id'] ?? null]);

        return $product;
    }

    public function update(string $id, array $form): array
    {
        $product = $this->catalog->updateProduct($id, $this->toPayload($form));

        Log::info('[ProductService] produto atualizado', ['productId' => $id]);

        return $product;
    }

    public function deactivate(string $id): void
    {
        $this->catalog->deleteProduct($id);

        Log::info('[ProductService] produto desativado', ['productId' => $id]);
    }

    private function toPayload(array $form): array
    {
        return [
            'name' => $form['name'],
            'description' => $form['description'] ?? null,
            'categoryId' => $form['category_id'],
            // o formulário trabalha em reais; a API, em centavos
            'priceCents' => (int) round($form['price'] * 100),
            'stock' => (int) $form['stock'],
            'active' => (bool) ($form['active'] ?? false),
        ];
    }
}
