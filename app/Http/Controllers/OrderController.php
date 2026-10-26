<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Services\SellerSession;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orders,
        private readonly SellerSession $session,
    ) {}

    public function index(Request $request): View
    {
        return view('orders.index', [
            'orders' => $this->orders->list((int) $request->query('page', 1)),
        ]);
    }

    public function show(string $id): View
    {
        return view('orders.show', [
            'order' => $this->orders->find($id),
            'sellerId' => $this->session->sellerId(),
        ]);
    }
}
