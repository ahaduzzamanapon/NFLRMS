<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('citizen_applicant')->after('email');
            $table->unsignedInteger('district_id')->nullable()->after('role');
            $table->unsignedInteger('upazila_id')->nullable()->after('district_id');

            // Add foreign key constraints manually since our geo tables have unsignedInteger IDs from the dump
            $table->foreign('district_id')->references('id')->on('districts')->nullOnDelete();
            $table->foreign('upazila_id')->references('id')->on('upazilas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['upazila_id']);
            $table->dropForeign(['district_id']);
            $table->dropColumn(['upazila_id', 'district_id', 'role']);
        });
    }
};
