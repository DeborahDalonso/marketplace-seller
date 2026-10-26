<?php

// Só as regras que os FormRequests do painel usam. Regra nova entra aqui junto.
return [
    'alpha_dash' => 'O campo :attribute só aceita letras, números, traço e sublinhado.',
    'boolean' => 'O campo :attribute precisa ser sim ou não.',
    'email' => 'Informe um e-mail válido em :attribute.',
    'image' => 'O arquivo de :attribute precisa ser uma imagem.',
    'integer' => 'O campo :attribute precisa ser um número inteiro.',
    'max' => [
        'numeric' => 'O campo :attribute não pode passar de :max.',
        'file' => 'O arquivo de :attribute não pode passar de :max KB.',
        'string' => 'O campo :attribute não pode passar de :max caracteres.',
    ],
    'min' => [
        'numeric' => 'O campo :attribute precisa ser no mínimo :min.',
        'string' => 'O campo :attribute precisa ter pelo menos :min caracteres.',
    ],
    'numeric' => 'O campo :attribute precisa ser um número.',
    'required' => 'O campo :attribute é obrigatório.',
    'string' => 'O campo :attribute precisa ser um texto.',
    'unique' => 'Este :attribute já está em uso.',

    'attributes' => [
        'email' => 'e-mail',
        'password' => 'senha',
        'name' => 'nome',
        'slug' => 'endereço da loja',
        'description' => 'descrição',
        'pix_key' => 'chave Pix',
        'logo' => 'logo',
        'category_id' => 'categoria',
        'price' => 'preço',
        'stock' => 'estoque',
        'active' => 'ativo',
    ],
];
