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
        if (Schema::hasTable('workflow_steps')) {
            Schema::table('workflow_steps', function (Blueprint $table) {
                if (! Schema::hasColumn('workflow_steps', 'role_keys')) {
                    $table->json('role_keys')->nullable()->after('role_name');
                }
                if (! Schema::hasColumn('workflow_steps', 'user_ids')) {
                    $table->json('user_ids')->nullable()->after('role_keys');
                }
                if (! Schema::hasColumn('workflow_steps', 'approval_type')) {
                    $table->string('approval_type')->default('all')->after('user_ids');
                }
            });
        }

        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                if (! Schema::hasColumn('applications', 'workflow_type_id')) {
                    $table->foreignId('workflow_type_id')->nullable()->after('applicant_type')->constrained('workflow_types')->nullOnDelete();
                }
                if (! Schema::hasColumn('applications', 'current_step_id')) {
                    $table->foreignId('current_step_id')->nullable()->after('current_actor_role')->constrained('workflow_steps')->nullOnDelete();
                }
                if (! Schema::hasColumn('applications', 'step_approvals')) {
                    $table->json('step_approvals')->nullable()->after('current_step_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('workflow_steps')) {
            Schema::table('workflow_steps', function (Blueprint $table) {
                if (Schema::hasColumn('workflow_steps', 'role_keys')) {
                    $table->dropColumn('role_keys');
                }
                if (Schema::hasColumn('workflow_steps', 'user_ids')) {
                    $table->dropColumn('user_ids');
                }
                if (Schema::hasColumn('workflow_steps', 'approval_type')) {
                    $table->dropColumn('approval_type');
                }
            });
        }

        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                if (Schema::hasColumn('applications', 'current_step_id')) {
                    $table->dropForeign(['current_step_id']);
                    $table->dropColumn('current_step_id');
                }
                if (Schema::hasColumn('applications', 'workflow_type_id')) {
                    $table->dropForeign(['workflow_type_id']);
                    $table->dropColumn('workflow_type_id');
                }
                if (Schema::hasColumn('applications', 'step_approvals')) {
                    $table->dropColumn('step_approvals');
                }
            });
        }
    }
};
