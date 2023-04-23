<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use TypeError;
use Vite;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'avatar',
        'description',
        'telegram_login',
        'telegram_id',
        'group_id',
    ];

    protected $hidden = [
        'remember_token',
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

        throw new TypeError('Unsupported type for $group parameter to hasGroup().');
    }

    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: function (?string $path) {
                return $path
                    ? Storage::disk('s3-avatar')->url($path)
                    : Vite::asset('resources/images/no-avatar.svg');
            },
        );
    }

    public function scopeAdmin(Builder $query): void
    {
        $query->whereGroupId(Group::GROUP_ADMIN);
    }
}
