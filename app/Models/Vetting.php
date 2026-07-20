<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vetting extends Model
{
    protected $fillable = [
        'application_id',
        'agency',
        'status',
        'remarks',
        'report_file',
        'vetted_by',
        'vetted_at',
    ];

    protected $casts = [
        'vetted_at' => 'datetime',
    ];

    /**
     * Get the application being vetted.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the officer who performed the vetting.
     */
    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vetted_by');
    }
}
