<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'avatar',
        'description',
        'telegram_login',
        'telegram_id',
        'group_id',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    /**
     * @param  string|int|Group  $roles
     */
    public function hasGroup($group): bool
    {
        $this->loadMissing('group');

        if (is_string($group)) {
            return $this->group->slug === $group;
        }

        if (is_int($group)) {
            return $this->group->id === $group;
        }

        if ($group instanceof Group) {
            return $this->group === $group;
        }

        throw new \TypeError('Unsupported type for $group parameter to hasGroup().');
    }
}
