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
        Schema::table('applications', function (Blueprint $table) {
            $table->boolean('service_fee_paid')->default(false)->after('remarks');
            $table->boolean('license_fee_paid')->default(false)->after('service_fee_paid');
            $table->unsignedInteger('service_fee_amount')->nullable()->after('license_fee_paid');
            $table->unsignedInteger('license_fee_amount')->nullable()->after('service_fee_amount');
            $table->json('payment_details')->nullable()->after('license_fee_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'service_fee_paid',
                'license_fee_paid',
                'service_fee_amount',
                'license_fee_amount',
                'payment_details',
            ]);
        });
    }
};
