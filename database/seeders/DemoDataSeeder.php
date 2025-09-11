<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Crea 1 usuario demo (puedes borrar si ya tienes usuarios vía WorkOS)
        $user = User::first() ?? User::create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'workos_id' => 'usr_demo_'.uniqid(),
            'avatar' => 'https://www.gravatar.com/avatar/'.md5('demo@example.com').'?d=identicon',
        ]);

        // Cuentas
        if ($user->accounts()->count() === 0) {
            Account::create([
                'user_id' => $user->id,
                'number'  => 'USD-'.str_pad((string) random_int(1, 99999999), 8, '0', STR_PAD_LEFT),
                'name'    => 'Cuenta principal',
                'currency'=> 'USD',
                'balance' => 5000,
                'is_primary' => true,
            ]);

            Account::create([
                'user_id' => $user->id,
                'number'  => 'USD-'.str_pad((string) random_int(1, 99999999), 8, '0', STR_PAD_LEFT),
                'name'    => 'Ahorros',
                'currency'=> 'USD',
                'balance' => 2000,
            ]);
        }

        // Comercios
        Merchant::upsert([
            ['name' => 'UtilityCo', 'category' => 'Utilities'],
            ['name' => 'NetFiber',  'category' => 'Internet'],
            ['name' => 'MobileX',   'category' => 'Telecom'],
        ], ['name']);
    }
}
