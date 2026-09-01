<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\ApplicationLog;
use App\Models\DealerStock;
use App\Models\License;
use App\Models\User;
use App\Models\Vetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        User::truncate();
        License::truncate();
        Application::truncate();
        ApplicationLog::truncate();
        Vetting::truncate();
        DealerStock::truncate();

        Schema::enableForeignKeyConstraints();

        $this->call([
            UserSeeder::class,
            ApplicationSeeder::class,
            CustomCommentSeeder::class,
            FeeConfigSeeder::class,
        ]);
    }
}
