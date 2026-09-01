<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_type_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('step_order')->default(1);
            $table->string('role_key');
            $table->string('role_name');
            $table->string('step_name');
            $table->boolean('can_approve')->default(true);
            $table->boolean('can_reject')->default(true);
            $table->boolean('can_return')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_steps');
    }
};
