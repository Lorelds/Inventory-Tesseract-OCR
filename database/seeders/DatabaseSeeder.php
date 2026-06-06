<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $company1 = \App\Models\Company::firstOrCreate(['name' => 'Default Company 1']);
        $company2 = \App\Models\Company::firstOrCreate(['name' => 'Default Company 2']);

        User::updateOrCreate(
            ['email' => 'owner@inventory.com'],
            [
                'name' => 'Owner (Super Admin)',
                'password' => bcrypt('password'),
                'role' => 'super_admin',
                'company_id' => $company1->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@inventory.com'],
            [
                'name' => 'Manager (Admin)',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'company_id' => $company2->id,
            ]
        );
    }
}
