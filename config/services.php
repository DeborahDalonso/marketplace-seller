<?php

return [

    /*
    |--------------------------------------------------------------------------
    | APIs do ecossistema
    |--------------------------------------------------------------------------
    |
    | O painel nao tem produtos nem pedidos no proprio banco: tudo vem destas
    | APIs, chamadas com o JWT do vendedor guardado na sessao. Os nomes das
    | variaveis sao os do CONTRATOS, iguais em todos os servicos.
    |
    */

    'auth' => [
        'url' => env('AUTH_URL', 'http://localhost:3003'),
    ],

    'catalog' => [
        'url' => env('CATALOG_URL', 'http://localhost:3001'),
    ],

    'orders' => [
        'url' => env('ORDERS_URL', 'http://localhost:3002'),
    ],

    'analytics' => [
        'url' => env('ANALYTICS_URL', 'http://localhost:3005'),
    ],

    'remote' => [
        'timeout_s' => (int) env('REMOTE_TIMEOUT_S', 5),
    ],

];
