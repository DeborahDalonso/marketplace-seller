<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class HealthService
{
    /**
     * @return array{service: string, version: string, mysql: string}
     */
    public function check(): array
    {
        return [
            'service' => 'marketplace-seller',
            'version' => config('app.version'),
            'mysql' => $this->mysql(),
        ];
    }

    public function healthy(array $report): bool
    {
        return $report['mysql'] === 'ok';
    }

    // Único try/catch de leitura no painel: aqui a falha é a resposta, não um erro
    private function mysql(): string
    {
        try {
            DB::select('select 1');

            return 'ok';
        } catch (Throwable $e) {
            Log::warning('[HealthService] banco fora', ['reason' => $e->getMessage()]);

            return 'error';
        }
    }
}
