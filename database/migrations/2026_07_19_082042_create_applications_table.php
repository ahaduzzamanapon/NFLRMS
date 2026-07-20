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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('new'); // new, renewal
            $table->string('applicant_type')->default('citizen'); // citizen, dealer
            $table->string('status')->default('draft');
            $table->unsignedInteger('district_id')->nullable();
            $table->unsignedInteger('upazila_id')->nullable();
            $table->json('applicant_details')->nullable();
            $table->json('firearm_details')->nullable();
            $table->json('documents')->nullable();
            $table->string('current_actor_role')->default('citizen_applicant');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('district_id')->references('id')->on('districts')->nullOnDelete();
            $table->foreign('upazila_id')->references('id')->on('upazilas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
