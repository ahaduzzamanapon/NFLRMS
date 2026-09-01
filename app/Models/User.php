<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name', 'name_bn', 'email', 'password', 'role', 'district_id', 'upazila_id',
    'nid', 'phone', 'dob', 'father_name', 'mother_name', 'spouse_name',
    'marital_status', 'nationality', 'religion', 'present_address',
    'permanent_address', 'occupation', 'employer_address',
    'edu_qualification', 'annual_income', 'tin_number', 'is_active',
    'profile_photo_path',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getRoleAttribute($value)
    {
        return Role::tryFrom($value) ?? $value;
    }

    public function setRoleAttribute($value)
    {
        $this->attributes['role'] = $value instanceof Role ? $value->value : $value;
    }

    public function roleLabel(): string
    {
        if ($this->role instanceof Role) {
            return $this->role->label();
        }
        $customRoles = json_decode(Setting::get('custom_roles', '{}'), true) ?: [];
        $roleStr = is_string($this->role) ? $this->role : '';

        return $customRoles[$roleStr] ?? ucfirst(str_replace('_', ' ', $roleStr));
    }

    /**
     * Get the district of the officer/user.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the upazila of the officer/user.
     */
    public function upazila(): BelongsTo
    {
        return $this->belongsTo(Upazila::class);
    }

    /**
     * Applications submitted by this user.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Licenses belonging to this user.
     */
    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }

    /**
     * Dealer stocks associated with this user.
     */
    public function dealerStocks(): HasMany
    {
        return $this->hasMany(DealerStock::class);
    }

    /**
     * Fields required to auto-fill a license application.
     *
     * @return array<string>
     */
    public static function requiredProfileFields(): array
    {
        return [
            'name_bn', 'nid', 'dob', 'father_name', 'mother_name',
            'marital_status', 'religion',
            'present_address', 'permanent_address',
            'district_id', 'upazila_id',
            'edu_qualification', 'occupation', 'employer_address',
            'annual_income', 'tin_number',
        ];
    }

    /**
     * Returns the list of required profile fields that are still empty.
     *
     * @return array<string>
     */
    public function profileMissingFields(): array
    {
        return array_filter(
            self::requiredProfileFields(),
            fn (string $field) => empty($this->attributes[$field] ?? $this->{$field})
        );
    }

    /**
     * Whether all fields needed for a license application have been filled.
     */
    public function isProfileComplete(): bool
    {
        return empty($this->profileMissingFields());
    }

    /**
     * Check if the user has the specified role(s).
     */
    public function hasRole(Role|string|array $roles): bool
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }

            return false;
        }

        $roleValue = $roles instanceof Role ? $roles->value : $roles;
        $userRoleValue = $this->role instanceof Role ? $this->role->value : $this->role;

        return $userRoleValue === $roleValue;
    }
}
