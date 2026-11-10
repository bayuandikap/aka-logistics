<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed roles, a demo administrator, and realistic Indonesian portfolio data.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->warn('Seeder akun dan data demo AKA Logistics hanya dijalankan di local/testing.');

            return;
        }

        $this->call(RoleSeeder::class);

        $admin = User::firstOrCreate(
            ['email' => 'admin@aka-logistics.test'],
            [
                'name' => 'Administrator AKA Logistics',
                'password' => 'password',
            ]
        );

        $admin->assignRole('super_admin');

        $this->call(PortfolioDemoSeeder::class);
    }
}
