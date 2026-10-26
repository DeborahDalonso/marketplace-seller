<?php

namespace Tests\Feature;

use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function fakeApis(array $sales, int $activeProducts = 0): void
    {
        Http::fake([
            'http://analytics.test/reports/sales*' => $this->envelope($sales),
            'http://catalog.test/products*' => $this->envelope([
                'items' => [], 'page' => 1, 'perPage' => 1, 'total' => $activeProducts,
            ]),
        ]);
    }

    public function test_mostra_cartoes_e_grafico_com_o_relatorio_fake(): void
    {
        $this->fakeApis([
            ['period' => now()->startOfMonth()->toDateString(), 'revenueCents' => 100000, 'orders' => 2, 'itemsSold' => 3],
            ['period' => now()->toDateString(), 'revenueCents' => 50050, 'orders' => 1, 'itemsSold' => 1],
        ], activeProducts: 7);
        Store::factory()->create(['seller_id' => self::SELLER_ID, 'name' => 'Casa da Ana']);

        $response = $this->actingAsSeller()->get('/');

        $response->assertOk()
            ->assertSeeInOrder([__('dashboard.revenue'), 'R$ 1.500,50'])
            ->assertSeeInOrder([__('dashboard.paid_orders'), '3'])
            ->assertSeeInOrder([__('dashboard.active_products'), '7'])
            ->assertSee('Casa da Ana')
            ->assertSee('class="sales-chart"', false)
            ->assertDontSee(__('dashboard.no_store_title'));

        // a série vai ao canvas com todos os dias do mês, zerando os sem venda
        $series = $response->viewData('summary')['series'];
        $this->assertCount((int) now()->day, $series);
        $this->assertSame(100000, $series[0]['revenueCents']);
        $this->assertSame(50050, end($series)['revenueCents']);

        Http::assertSent(fn (Request $request) => str_starts_with($request->url(), 'http://analytics.test/reports/sales?')
            && $request->data()['sellerId'] === self::SELLER_ID
            && $request->data()['groupBy'] === 'day'
            && $request->hasHeader('Authorization', 'Bearer '.self::SELLER_TOKEN));
    }

    public function test_sem_vendas_mostra_estado_vazio_do_grafico(): void
    {
        $this->fakeApis([]);

        $this->actingAsSeller()
            ->get('/')
            ->assertOk()
            ->assertSee('R$ 0,00')
            ->assertSee(__('dashboard.chart_empty_title'))
            ->assertDontSee('class="sales-chart"', false);
    }

    public function test_sem_loja_convida_a_criar(): void
    {
        $this->fakeApis([]);

        $this->actingAsSeller()
            ->get('/')
            ->assertOk()
            ->assertSee(__('dashboard.no_store_title'))
            ->assertSee(route('store.edit'));
    }
}
