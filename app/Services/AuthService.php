<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthService
{
    // REGRA: comprador não entra no painel, mesmo com credencial válida
    private const ALLOWED_ROLES = ['seller', 'admin'];

    public function __construct(
        private readonly AuthClient $client,
        private readonly SellerSession $session,
    ) {}

    /**
     * Autentica no serviço auth e guarda token e usuário na sessão.
     * Credencial errada ou papel sem acesso viram erro de validação no
     * campo de e-mail, como o próprio Laravel faz no login nativo.
     */
    public function login(string $email, string $password): void
    {
        $data = $this->client->login($email, $password);

        if ($data === null) {
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        $user = $data['user'] ?? [];

        if (! in_array($user['role'] ?? null, self::ALLOWED_ROLES, true)) {
            Log::info('[AuthService] login recusado por papel', ['email' => $email, 'role' => $user['role'] ?? null]);

            throw ValidationException::withMessages(['email' => __('auth.role_not_allowed')]);
        }

        $this->session->store($data['accessToken'], $user);

        Log::info('[AuthService] vendedor entrou', ['sellerId' => $user['id'] ?? null]);
    }

    public function logout(): void
    {
        Log::info('[AuthService] vendedor saiu', ['sellerId' => $this->session->sellerId()]);

        $this->session->clear();
    }
}
