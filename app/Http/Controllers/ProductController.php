<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $products) {}

    public function index(Request $request): View
    {
        return view('products.index', [
            'products' => $this->products->list((int) $request->query('page', 1)),
        ]);
    }

    public function create(): View
    {
        return view('products.create', [
            'categories' => $this->products->categories(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $this->products->create($request->validated());

        return redirect()->route('products.index')->with('success', __('products.created'));
    }

    public function edit(string $product): View
    {
        return view('products.edit', [
            'product' => $this->products->find($product),
            'categories' => $this->products->categories(),
        ]);
    }

    public function update(ProductRequest $request, string $product): RedirectResponse
    {
        $this->products->update($product, $request->validated());

        return redirect()->route('products.index')->with('success', __('products.updated'));
    }

    public function destroy(string $product): RedirectResponse
    {
        $this->products->deactivate($product);

        return redirect()->route('products.index')->with('success', __('products.deactivated'));
    }
}
