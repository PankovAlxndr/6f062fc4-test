<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    public const GROUP_NEW = 1;

    public const GROUP_ADMIN = 2;

    protected $fillable = [
        'name',
        'slug',
        'is_not_delete',
    ];

    protected $casts = [
        'is_not_delete' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'group_id');
    }

    public function isAllowDelete(): bool
    {
        return ! $this->is_not_delete;
    }
}
