<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowStep extends Model
{
    protected $fillable = [
        'workflow_type_id', 'step_order', 'role_key', 'role_name',
        'role_keys', 'user_ids', 'approval_type',
        'step_name', 'can_approve', 'can_reject', 'can_return', 'is_active',
    ];

    protected $casts = [
        'role_keys' => 'array',
        'user_ids' => 'array',
        'can_approve' => 'boolean',
        'can_reject' => 'boolean',
        'can_return' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function workflowType(): BelongsTo
    {
        return $this->belongsTo(WorkflowType::class);
    }

    /**
     * Get all role keys assigned to this step (single or parallel multi-role).
     *
     * @return array<int, string>
     */
    public function getAllRoleKeys(): array
    {
        if (! empty($this->role_keys) && is_array($this->role_keys)) {
            return array_values(array_unique(array_filter($this->role_keys)));
        }

        return ! empty($this->role_key) ? [$this->role_key] : [];
    }

    /**
     * Check if this step is assigned to multiple parallel approver roles.
     */
    public function isMultiRole(): bool
    {
        return count($this->getAllRoleKeys()) > 1;
    }

    /**
     * Primary role key for routing or fallback actor.
     */
    public function primaryRoleKey(): string
    {
        $keys = $this->getAllRoleKeys();

        return $keys[0] ?? $this->role_key ?? 'dc_front_desk';
    }
}
