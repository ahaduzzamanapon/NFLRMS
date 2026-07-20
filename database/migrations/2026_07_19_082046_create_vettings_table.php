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
        Schema::create('vettings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('agency'); // police, sb, nsi, dgfi
            $table->string('status')->default('pending'); // pending, cleared, flagged
            $table->text('remarks')->nullable();
            $table->string('report_file')->nullable();
            $table->foreignId('vetted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('vetted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vettings');
    }
};
