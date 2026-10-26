<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // O painel não tem usuário local: quem entra é o vendedor do marketplace-auth.
    // A loja é criada pelo próprio vendedor na primeira entrada, então não há seed.
    public function run(): void {}
}
