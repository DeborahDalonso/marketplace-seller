<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Services\SellerSession;
use App\Services\StoreService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboard,
        private readonly StoreService $stores,
        private readonly SellerSession $session,
    ) {}

    public function __invoke(): View
    {
        return view('dashboard.index', [
            'summary' => $this->dashboard->summary(),
            'store' => $this->stores->findForSeller($this->session->sellerId()),
        ]);
    }
}
