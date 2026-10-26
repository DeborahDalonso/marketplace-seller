<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use App\Services\SellerSession;
use App\Services\StoreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function __construct(
        private readonly StoreService $stores,
        private readonly SellerSession $session,
    ) {}

    public function edit(): View
    {
        return view('store.edit', [
            'store' => $this->stores->findForSeller($this->session->sellerId()),
        ]);
    }

    public function update(StoreRequest $request): RedirectResponse
    {
        $store = $this->stores->save(
            $this->session->sellerId(),
            $request->safe()->except('logo'),
            $request->file('logo'),
        );

        return redirect()
            ->route('store.edit')
            ->with('success', __($store->wasRecentlyCreated ? 'store.created' : 'store.updated'));
    }
}
