<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    protected $fillable = ['division_id', 'name', 'bn_name', 'lat', 'lng', 'website'];

    /**
     * Get the division this district belongs to.
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Get upazilas within the district.
     */
    public function upazilas(): HasMany
    {
        return $this->hasMany(Upazila::class);
    }

    /**
     * Get users under this district.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get applications under this district.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}

