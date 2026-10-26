<?php

namespace App\Services;

/**
 * Único lugar que sabe como o vendedor logado fica guardado na sessão.
 * O token nunca vai para cookie legível: fica no storage da sessão do Laravel.
 */
class SellerSession
{
    private const TOKEN_KEY = 'seller.token';

    private const USER_KEY = 'seller.user';

    public function check(): bool
    {
        return session()->has(self::TOKEN_KEY);
    }

    public function token(): ?string
    {
        return session(self::TOKEN_KEY);
    }

    /**
     * @return array{id: string, email: string, name: string, role: string}|null
     */
    public function user(): ?array
    {
        return session(self::USER_KEY);
    }

    public function sellerId(): ?string
    {
        return $this->user()['id'] ?? null;
    }

    public function store(string $token, array $user): void
    {
        session([self::TOKEN_KEY => $token, self::USER_KEY => $user]);
    }

    public function clear(): void
    {
        session()->invalidate();
        session()->regenerateToken();
    }
}
