<?php

namespace Database\Seeders;

use App\Models\CentralSuperadmin;
use App\Models\Tenant;
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
        CentralSuperadmin::query()->updateOrCreate(
            ['email' => env('CENTRAL_SUPERADMIN_EMAIL', 'superadmin@buksu.test')],
            [
                'name' => env('CENTRAL_SUPERADMIN_NAME', 'BukSU Superadmin'),
                'password' => env('CENTRAL_SUPERADMIN_PASSWORD', 'password123'),
            ]
        );

        Tenant::query()->updateOrCreate(
            ['slug' => 'college-of-technologies'],
            [
                'name' => 'College of Technologies',
                'code' => 'COT',
                'domain' => env('TENANT_DOMAIN', 'technology.buksu.test'),
                'subdomain' => 'technology',
                'plan' => 'premium',
                'subscription_starts_at' => now()->toDateString(),
                'subscription_expires_at' => now()->addYear()->toDateString(),
                'database' => env('TENANT_DB_DATABASE', 'buksu_college_of_technologies'),
                'db_host' => env('TENANT_DB_HOST', '127.0.0.1'),
                'db_port' => env('TENANT_DB_PORT', '3306'),
                'db_username' => env('TENANT_DB_USERNAME', 'root'),
                'db_password' => env('TENANT_DB_PASSWORD', ''),
                'settings' => [
                    'focus' => 'College of Technologies',
                    'branding' => [
                        'accent' => '#dc2626',
                        'secondary' => '#ffffff',
                    ],
                ],
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password123',
            ]
        );
    }
}
