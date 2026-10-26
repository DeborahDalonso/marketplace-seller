<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthTest extends TestCase
{
    use RefreshDatabase;

    public function test_responde_envelope_com_banco_ok(): void
    {
        $this->getJson('/health')
            ->assertOk()
            ->assertExactJson([
                'success' => true,
                'message' => null,
                'data' => [
                    'service' => 'marketplace-seller',
                    'version' => config('app.version'),
                    'mysql' => 'ok',
                ],
            ]);
    }

    public function test_e_publico(): void
    {
        $this->get('/health')->assertOk();
    }
}
