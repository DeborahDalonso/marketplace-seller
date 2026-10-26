<?php

namespace App\Services;

class AnalyticsClient extends RemoteClient
{
    protected function service(): string
    {
        return 'analytics';
    }

    public function sales(array $query): array
    {
        return $this->send('get', '/reports/sales', $query);
    }
}
