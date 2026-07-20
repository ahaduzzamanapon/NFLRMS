<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing tables if they exist to avoid conflicts
        Schema::dropIfExists('upazilas');
        Schema::dropIfExists('thanas');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('divisions');

        if (DB::getDriverName() === 'sqlite') {
            Schema::create('divisions', function ($table) {
                $table->increments('id');
                $table->string('name');
                $table->string('bn_name')->nullable();
                $table->string('url')->nullable();
                $table->timestamps();
            });

            Schema::create('districts', function ($table) {
                $table->increments('id');
                $table->unsignedInteger('division_id');
                $table->string('name');
                $table->string('bn_name')->nullable();
                $table->double('lat')->nullable();
                $table->double('lon')->nullable();
                $table->string('url')->nullable();
                $table->timestamps();
            });

            Schema::create('upazilas', function ($table) {
                $table->increments('id');
                $table->unsignedInteger('district_id');
                $table->string('name');
                $table->string('bn_name')->nullable();
                $table->double('lat')->nullable();
                $table->double('lon')->nullable();
                $table->string('url')->nullable();
                $table->timestamps();
            });

            $divId = DB::table('divisions')->insertGetId([
                'name' => 'Dhaka Division',
                'bn_name' => 'ঢাকা বিভাগ',
            ]);

            $districts = ['Dhaka', 'Chattogram', 'Sylhet', 'Rajshahi', 'Khulna'];
            foreach ($districts as $dName) {
                $dstId = DB::table('districts')->insertGetId([
                    'division_id' => $divId,
                    'name' => $dName,
                    'bn_name' => $dName,
                ]);

                DB::table('upazilas')->insert([
                    'district_id' => $dstId,
                    'name' => $dName . ' Sadar',
                    'bn_name' => $dName . ' Sadar',
                ]);
            }
        } else {
            // Read and execute the SQL file
            $sqlPath = database_path('seeders/db_geocode.sql');
            if (file_exists($sqlPath)) {
                $sql = file_get_contents($sqlPath);
                DB::unprepared($sql);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upazilas');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('divisions');
    }
};
