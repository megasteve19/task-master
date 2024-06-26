<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Scout\Searchable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasUlids;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
    ];

    protected $appends = [
        'avatar',
        'is_owner',
        'is_admin',
        'is_user',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'role' => UserRole::class,
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getAvatarAttribute()
    {
        return "https://source.boringavatars.com/beam/128/{$this->name}.png?square";
    }

    public function getIsOwnerAttribute(): bool
    {
        return $this->role === UserRole::Owner;
    }

    public function getIsAdminAttribute(): bool
    {
        return in_array($this->role, [UserRole::Owner, UserRole::Admin]);
    }

    public function getIsUserAttribute(): bool
    {
        return $this->role === UserRole::User;
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'assignee_project');
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'assignee_task');
    }

    public function isAdministator(): bool
    {
        return in_array($this->role, [UserRole::Owner, UserRole::Admin]);
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role->prettyName(),
            'created_at' => is_string($this->created_at) ? Carbon::parse($this->created_at)->timestamp : $this->created_at->timestamp,
        ];
    }
}
