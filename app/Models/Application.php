<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Application extends Model
{
    protected $fillable = [
        'application_number',
        'user_id',
        'dealer_id',
        'type',
        'applicant_type',
        'status',
        'district_id',
        'upazila_id',
        'applicant_details',
        'firearm_details',
        'documents',
        'current_actor_role',
        'remarks',
        'service_fee_paid',
        'license_fee_paid',
        'service_fee_amount',
        'license_fee_amount',
        'payment_details',
    ];

    protected $casts = [
        'applicant_details' => 'array',
        'firearm_details' => 'array',
        'documents' => 'array',
        'payment_details' => 'array',
    ];

    /**
     * Get the applicant user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sourcing dealer user.
     */
    public function dealer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dealer_id');
    }

    /**
     * Get the district of this application.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the upazila of this application.
     */
    public function upazila(): BelongsTo
    {
        return $this->belongsTo(Upazila::class);
    }

    /**
     * Vettings requested for this application.
     */
    public function vettings(): HasMany
    {
        return $this->hasMany(Vetting::class);
    }

    /**
     * The issued license for this application, if any.
     */
    public function license(): HasOne
    {
        return $this->hasOne(License::class);
    }

    /**
     * Status logs for this application.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(ApplicationLog::class)->orderBy('created_at', 'desc');
    }
}
