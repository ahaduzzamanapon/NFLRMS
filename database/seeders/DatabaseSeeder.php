<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        \App\Models\User::truncate();
        \App\Models\License::truncate();
        \App\Models\Application::truncate();
        \App\Models\ApplicationLog::truncate();
        \App\Models\Vetting::truncate();
        \App\Models\DealerStock::truncate();

        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $this->call([
            UserSeeder::class,
            ApplicationSeeder::class,
        ]);
    }
}
