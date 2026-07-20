<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Upazila extends Model
{
    protected $fillable = ['district_id', 'name', 'bn_name', 'lat', 'lng'];

    /**
     * Get the district this upazila belongs to.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get users under this upazila.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get applications under this upazila.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
