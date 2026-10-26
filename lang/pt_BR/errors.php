<?php

// Chaves usadas pela RemoteApiException: <serviço>.unavailable e remote.<motivo>.
// A mensagem é o que aparece na tela; a causa técnica fica no log.
return [
    'auth' => [
        'required' => 'Entre com sua conta de vendedor para continuar.',
        'expired' => 'Sua sessão expirou. Entre de novo.',
        'unavailable' => 'O serviço de login não respondeu. Tente de novo em instantes.',
    ],
    'catalog' => [
        'unavailable' => 'O catálogo não respondeu. Seus produtos continuam salvos; tente de novo em instantes.',
    ],
    'orders' => [
        'unavailable' => 'O serviço de pedidos não respondeu. Tente de novo em instantes.',
    ],
    'analytics' => [
        'unavailable' => 'Os relatórios não responderam. Os números do painel voltam assim que o serviço responder.',
    ],
    'remote' => [
        'forbidden' => 'Você não tem permissão para mexer neste item.',
        'not_found' => 'Este item não existe mais ou não é seu.',
        'conflict' => 'Já existe um item com estes dados.',
        'validation' => 'Alguns campos não foram aceitos. Confira os destaques.',
    ],
    'page' => [
        'title' => 'Não deu para carregar esta página',
        'retry' => 'Tentar de novo',
        'home' => 'Voltar ao início',
    ],
];
