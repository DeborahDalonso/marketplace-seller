<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use App\Services\SellerSession;
use App\Services\StoreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $auth,
        private readonly StoreService $stores,
        private readonly SellerSession $session,
    ) {}

    public function create(): View|RedirectResponse
    {
        if ($this->session->check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $this->auth->login($request->validated('email'), $request->validated('password'));
        $request->session()->regenerate();

        // Primeira entrada: sem loja, o vendedor vai direto criar a dele
        if (! $this->stores->findForSeller($this->session->sellerId())) {
            return redirect()->route('store.edit')->with('info', __('store.first_time'));
        }

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(): RedirectResponse
    {
        $this->auth->logout();

        return redirect()->route('login')->with('success', __('login.logged_out'));
    }
}
