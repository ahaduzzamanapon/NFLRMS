<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nid')->nullable()->after('name');
            $table->string('phone')->nullable()->after('nid');
            $table->date('dob')->nullable()->after('phone');
            $table->string('father_name')->nullable()->after('dob');
            $table->string('mother_name')->nullable()->after('father_name');
            $table->string('spouse_name')->nullable()->after('mother_name');
            $table->string('marital_status')->nullable()->after('spouse_name');
            $table->string('nationality')->default('Bangladeshi')->after('marital_status');
            $table->string('religion')->nullable()->after('nationality');
            $table->text('present_address')->nullable()->after('religion');
            $table->text('permanent_address')->nullable()->after('present_address');
            $table->string('occupation')->nullable()->after('permanent_address');
            $table->string('employer_address')->nullable()->after('occupation');
            $table->string('edu_qualification')->nullable()->after('employer_address');
            $table->unsignedBigInteger('annual_income')->nullable()->after('edu_qualification');
            $table->string('tin_number')->nullable()->after('annual_income');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nid', 'phone', 'dob', 'father_name', 'mother_name',
                'spouse_name', 'marital_status', 'nationality', 'religion',
                'present_address', 'permanent_address', 'occupation',
                'employer_address', 'edu_qualification', 'annual_income', 'tin_number',
            ]);
        });
    }
};

