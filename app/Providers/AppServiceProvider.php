<?php

namespace App\Providers;

use App\Services\AnalyticsClient;
use App\Services\AuthClient;
use App\Services\CatalogClient;
use App\Services\OrdersClient;
use App\Services\SellerSession;
use App\Services\StoreService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    private const CLIENTS = [
        AuthClient::class => 'auth',
        CatalogClient::class => 'catalog',
        OrdersClient::class => 'orders',
        AnalyticsClient::class => 'analytics',
    ];

    public function register(): void
    {
        // Cada client só precisa da URL da própria API; o resto o container resolve
        foreach (self::CLIENTS as $client => $service) {
            $this->app->bind($client, fn (Application $app) => new $client(
                config("services.{$service}.url"),
                $app->make(SellerSession::class),
            ));
        }
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // A API trabalha em centavos e UTC; a view mostra reais e horário de Brasília
        Blade::directive('money', fn (string $cents) => sprintf(
            "<?php echo 'R$ '.number_format((%s) / 100, 2, ',', '.'); ?>",
            $cents,
        ));
        Blade::directive('datetime', fn (string $value) => sprintf(
            "<?php echo %s::parse(%s)->timezone(config('app.timezone'))->format('d/m/Y H:i'); ?>",
            Carbon::class,
            $value,
        ));

        View::composer('layouts.app', function (ViewInstance $view) {
            $session = $this->app->make(SellerSession::class);

            $view->with('currentUser', $session->user());
            $view->with('currentStore', $session->check()
                ? $this->app->make(StoreService::class)->findForSeller($session->sellerId())
                : null);
        });
    }
}
