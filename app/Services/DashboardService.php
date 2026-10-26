<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;

class DashboardService
{
    public function __construct(
        private readonly AnalyticsClient $analytics,
        private readonly CatalogClient $catalog,
        private readonly SellerSession $session,
    ) {}

    /**
     * Números do mês corrente. O analytics devolve só os dias com venda
     * ([{ period, revenueCents, orders, itemsSold }]); os cartões somam a
     * série e o gráfico recebe todos os dias do mês, com zero onde não vendeu.
     *
     * @return array{revenueCents: int, paidOrders: int, activeProducts: int, series: array}
     */
    public function summary(): array
    {
        $sellerId = $this->session->sellerId();
        $monthStart = now()->startOfMonth();

        $sales = $this->analytics->sales([
            'sellerId' => $sellerId,
            'groupBy' => 'day',
            // o analytics trabalha em UTC e assume "agora" quando "to" não vem
            'from' => $monthStart->copy()->utc()->toIso8601String(),
        ]);

        // só o total interessa; perPage 1 evita baixar a lista inteira
        $products = $this->catalog->products(['sellerId' => $sellerId, 'active' => 'true', 'perPage' => 1]);

        return [
            'revenueCents' => (int) array_sum(array_column($sales, 'revenueCents')),
            'paidOrders' => (int) array_sum(array_column($sales, 'orders')),
            'activeProducts' => $products->total(),
            'series' => $this->fillDays($sales, $monthStart),
        ];
    }

    private function fillDays(array $sales, CarbonInterface $from): array
    {
        $byDay = array_column($sales, null, 'period');
        $days = [];

        foreach (CarbonPeriod::create($from, now()) as $day) {
            $key = $day->toDateString();
            $days[] = [
                'date' => $key,
                'revenueCents' => (int) ($byDay[$key]['revenueCents'] ?? 0),
                'orders' => (int) ($byDay[$key]['orders'] ?? 0),
            ];
        }

        return $days;
    }
}
