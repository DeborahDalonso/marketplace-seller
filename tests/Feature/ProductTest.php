<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private function product(array $overrides = []): array
    {
        return $overrides + [
            'id' => 'prd_01',
            'sellerId' => self::SELLER_ID,
            'categoryId' => 'cat_01',
            'name' => 'Fone Bluetooth',
            'slug' => 'fone-bluetooth',
            'description' => 'Com cancelamento de ruído',
            'priceCents' => 19990,
            'stock' => 12,
            'active' => true,
        ];
    }

    private function categories(): array
    {
        return ['http://catalog.test/categories' => $this->envelope([
            ['id' => 'cat_01', 'name' => 'Eletrônicos'],
            ['id' => 'cat_02', 'name' => 'Casa'],
        ])];
    }

    public function test_lista_renderiza_o_que_o_catalog_devolve(): void
    {
        Http::fake($this->categories() + [
            'http://catalog.test/products*' => $this->envelope([
                'items' => [$this->product(), $this->product(['id' => 'prd_02', 'name' => 'Caneca', 'stock' => 0, 'active' => false, 'priceCents' => 3500])],
                'page' => 1,
                'perPage' => 20,
                'total' => 2,
            ]),
        ]);

        $this->actingAsSeller()
            ->get('/products')
            ->assertOk()
            ->assertSeeInOrder(['Fone Bluetooth', 'Eletrônicos', 'R$ 199,90', '12 un.', __('ui.status.active')])
            ->assertSeeInOrder(['Caneca', 'R$ 35,00', __('products.stock_out'), __('ui.status.inactive')]);

        Http::assertSent(fn (Request $request) => str_starts_with($request->url(), 'http://catalog.test/products?')
            && $request->hasHeader('Authorization', 'Bearer '.self::SELLER_TOKEN)
            && $request->data()['sellerId'] === self::SELLER_ID);
    }

    public function test_lista_vazia_mostra_estado_vazio(): void
    {
        Http::fake($this->categories() + [
            'http://catalog.test/products*' => $this->envelope(['items' => [], 'page' => 1, 'perPage' => 20, 'total' => 0]),
        ]);

        $this->actingAsSeller()
            ->get('/products')
            ->assertOk()
            ->assertSee(__('products.empty_title'));
    }

    public function test_formulario_de_criacao_lista_as_categorias(): void
    {
        Http::fake($this->categories());

        $this->actingAsSeller()
            ->get('/products/create')
            ->assertOk()
            ->assertSee('Eletrônicos')
            ->assertSee('Casa');
    }

    public function test_criar_produto_chama_o_catalog_com_o_token_e_centavos(): void
    {
        Http::fake(['http://catalog.test/products' => $this->envelope($this->product(), 201)]);

        $this->actingAsSeller()
            ->post('/products', [
                'name' => 'Fone Bluetooth',
                'description' => 'Com cancelamento de ruído',
                'category_id' => 'cat_01',
                'price' => '199.90',
                'stock' => '12',
                'active' => '1',
            ])
            ->assertRedirect('/products')
            ->assertSessionHas('success', __('products.created'));

        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && $request->url() === 'http://catalog.test/products'
            && $request->hasHeader('Authorization', 'Bearer '.self::SELLER_TOKEN)
            && $request['priceCents'] === 19990
            && $request['categoryId'] === 'cat_01'
            && $request['stock'] === 12
            && $request['active'] === true);
    }

    public function test_formulario_invalido_nao_chama_a_api(): void
    {
        Http::fake();

        $this->actingAsSeller()
            ->from('/products/create')
            ->post('/products', ['name' => '', 'price' => 'abc', 'stock' => -1])
            ->assertRedirect('/products/create')
            ->assertSessionHasErrors(['name', 'category_id', 'price', 'stock']);

        Http::assertNothingSent();
    }

    public function test_editar_produto_carrega_dados_e_envia_put(): void
    {
        Http::fake($this->categories() + [
            'http://catalog.test/products/prd_01' => $this->envelope($this->product()),
        ]);

        $this->actingAsSeller()
            ->get('/products/prd_01/edit')
            ->assertOk()
            ->assertSee('value="Fone Bluetooth"', false)
            ->assertSee('value="199.90"', false);

        $this->actingAsSeller()
            ->put('/products/prd_01', [
                'name' => 'Fone novo',
                'category_id' => 'cat_02',
                'price' => '150',
                'stock' => '3',
            ])
            ->assertRedirect('/products')
            ->assertSessionHas('success', __('products.updated'));

        Http::assertSent(fn (Request $request) => $request->method() === 'PUT'
            && $request->url() === 'http://catalog.test/products/prd_01'
            && $request['priceCents'] === 15000
            && $request['active'] === false);
    }

    public function test_desativar_produto_chama_delete_no_catalog(): void
    {
        Http::fake(['http://catalog.test/products/prd_01' => $this->envelope(null)]);

        $this->actingAsSeller()
            ->delete('/products/prd_01')
            ->assertRedirect('/products')
            ->assertSessionHas('success', __('products.deactivated'));

        Http::assertSent(fn (Request $request) => $request->method() === 'DELETE'
            && $request->url() === 'http://catalog.test/products/prd_01');
    }
}
