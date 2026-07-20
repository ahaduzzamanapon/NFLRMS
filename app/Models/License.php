<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class License extends Model
{
    protected $fillable = [
        'license_number',
        'user_id',
        'application_id',
        'type',
        'issue_date',
        'expiry_date',
        'status',
        'firearm_details',
        'qrcode',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'firearm_details' => 'array',
    ];

    /**
     * Get the license owner user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the application that resulted in this license.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
