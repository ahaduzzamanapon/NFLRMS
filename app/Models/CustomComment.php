<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomComment extends Model
{
    protected $fillable = [
        'title',
        'comment',
        'user_id',
    ];

    /**
     * Get the user who created this custom comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
