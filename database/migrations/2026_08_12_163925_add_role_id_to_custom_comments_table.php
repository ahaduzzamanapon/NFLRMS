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
        Schema::table('custom_comments', function (Blueprint $table) {
            // Nullable: null = visible only to creator; value = target role key (e.g. 'dc_front_desk')
            $table->string('role_id')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_comments', function (Blueprint $table) {
            $table->dropColumn('role_id');
        });
    }
};
