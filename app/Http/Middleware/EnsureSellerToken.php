<?php

namespace App\Http\Middleware;

use App\Services\SellerSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSellerToken
{
    public function __construct(private readonly SellerSession $session) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->session->check()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => __('errors.auth.required'),
                'errors' => [],
            ], 401);
        }

        return redirect()->guest(route('login'));
    }
}
