<?php

namespace App\Services;

class AuthClient extends RemoteClient
{
    protected function service(): string
    {
        return 'auth';
    }

    /**
     * Devolve { accessToken, refreshToken, user } ou null quando a credencial
     * está errada. Credencial errada não é falha da API: o formulário responde.
     */
    public function login(string $email, string $password): ?array
    {
        $response = $this->call('post', '/auth/login', ['email' => $email, 'password' => $password]);

        if ($response->status() === 401) {
            return null;
        }

        return $this->unwrap($response, '/auth/login');
    }
}
