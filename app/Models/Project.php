<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUlids;
    use Searchable;

    protected $fillable = [
        'name',
        'description',
        'due_date',
        'is_archived',
    ];

    protected $appends = [
        'is_overdue',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
        'due_date' => 'datetime',
    ];

    public function getIsOverdueAttribute()
    {
        if(!$this->due_date) {
            return false;
        }

        return $this->due_date < now();
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'assignees' => $this->assignees()->pluck('name')->toArray(),
            'created_at' => $this->created_at->timestamp,
        ];
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'assignee_project');
    }

    public function scopeActive(Builder $builder)
    {
        return $builder->where('is_archived', false)
            ->where(fn (Builder $builder) => $builder->whereNull('due_date')->orWhere('due_date', '>', now()));
    }

    public function scopeArchived(Builder $builder)
    {
        return $builder->where('is_archived', true);
    }

    public function scopeOverdue(Builder $builder)
    {
        return $builder->where('due_date', '<=', now());
    }

    public function scopeAccessibleBy(Builder $builder, User $user)
    {
        if(in_array($user->role, [UserRole::Owner, UserRole::Admin])) {
            return $builder;
        }

        return $builder->whereHas('assignees', function (Builder $builder) use ($user) {
            $builder->where('user_id', $user->id);
        });
    }
}
