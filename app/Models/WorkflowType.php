<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowType extends Model
{
    protected $fillable = ['key', 'name', 'name_bn', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function steps(): HasMany
    {
        return $this->hasMany(WorkflowStep::class)->orderBy('step_order');
    }

    public function activeSteps(): HasMany
    {
        return $this->hasMany(WorkflowStep::class)->where('is_active', true)->orderBy('step_order');
    }
}
