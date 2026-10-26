<?php

namespace Tests;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    protected const SELLER_ID = 'usr_01J9X0000000000000000SELLR';

    protected const SELLER_TOKEN = 'tok_teste';

    protected function setUp(): void
    {
        parent::setUp();

        // Nenhum teste pode bater numa API de verdade; cada um declara o próprio fake
        Http::preventStrayRequests();
    }

    /**
     * Sessão de vendedor logado, igual à que AuthService::login grava.
     */
    protected function actingAsSeller(array $user = []): static
    {
        return $this->withSession([
            'seller.token' => self::SELLER_TOKEN,
            'seller.user' => $user + [
                'id' => self::SELLER_ID,
                'email' => 'ana@example.com',
                'name' => 'Ana',
                'role' => 'seller',
            ],
        ]);
    }

    /**
     * Resposta de sucesso no envelope do CONTRATOS, como as APIs devolvem.
     */
    protected function envelope(mixed $data, int $status = 200): PromiseInterface
    {
        return Http::response(['success' => true, 'message' => null, 'data' => $data], $status);
    }

    protected function failure(string $message, int $status, array $errors = []): PromiseInterface
    {
        return Http::response(['success' => false, 'message' => $message, 'errors' => $errors], $status);
    }
}
