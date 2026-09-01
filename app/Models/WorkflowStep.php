<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowStep extends Model
{
    protected $fillable = [
        'workflow_type_id', 'step_order', 'role_key', 'role_name',
        'step_name', 'can_approve', 'can_reject', 'can_return', 'is_active',
    ];

    protected $casts = [
        'can_approve' => 'boolean',
        'can_reject' => 'boolean',
        'can_return' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function workflowType(): BelongsTo
    {
        return $this->belongsTo(WorkflowType::class);
    }
}
