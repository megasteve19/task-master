<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Project extends Model
{
    use HasFactory;
    use HasUlids;
    use Searchable;
    use SoftDeletes {
        forceDelete as protected parentForceDelete;
        restore as protected parentRestore;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'description',
        'due_date',
        'is_archived',
    ];

    /**
     * Virtual attributes to append.
     *
     * @var string[]
     */
    protected $appends = [
        'is_overdue',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var string[]
     */
    protected $casts = [
        'is_archived' => 'boolean',
        'due_date' => 'datetime',
    ];

    /**
     * The accessor of the `is_overdue` attribute.
     *
     * @var string[]
     */
    public function getIsOverdueAttribute()
    {
        if (!$this->due_date)
        {
            return false;
        }

        return $this->due_date < now();
    }

    /**
     * Tasks of the project.
     *
     * @return HasMany
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Assignees of the project.
     *
     * @return BelongsToMany
     */
    public function assignees()
    {
        return $this->belongsToMany(User::class, 'assignee_project');
    }

    /**
     * Scope a query to only include active projects.
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeActive(Builder $builder)
    {
        return $builder->where('is_archived', false);
    }

    /**
     * Scope a query to only include archived projects.
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeArchived(Builder $builder)
    {
        return $builder->where('is_archived', true);
    }

    /**
     * Scope a query to only include overdue projects.
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeOverdue(Builder $builder)
    {
        return $builder->where('due_date', '<=', now());
    }

    /**
     * Scope a query to only include projects accessible by the user.
     *
     * @param Builder $builder
     * @param User $user
     *
     * @return Builder
     */
    public function scopeAccessibleBy(Builder $builder, User $user)
    {
        if (in_array($user->role, [UserRole::Owner, UserRole::Admin]))
        {
            return $builder;
        }

        return $builder->whereHas('assignees', function(Builder $builder) use ($user) {
            $builder->where('user_id', $user->id);
        });
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
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

    /**
     * Archive the project and its tasks.
     *
     * @return bool
     */
    public function archive(): bool
    {
        $this->is_archived = true;

        if ($saved = $this->save())
        {
            $this->tasks()->update(['is_archived' => true]);
        }

        return $saved;
    }

    /**
     * Restore the project from the archive and its tasks.
     *
     * @return bool
     */
    public function restoreFromArchive(): bool
    {
        $this->is_archived = false;

        if ($saved = $this->save())
        {
            $this->tasks()->update(['is_archived' => false]);
        }

        return $saved;
    }

    /**
     * Delete the project and its tasks.
     *
     * @return bool|null
     */
    public function delete(): bool|null
    {
        $this->tasks()->delete();

        return parent::delete();
    }

    /**
     * Permanently delete the project and its tasks.
     *
     * @return bool|null
     */
    public function forceDelete(): bool|null
    {
        $this->tasks()->forceDelete();

        return $this->parentForceDelete();
    }

    /**
     * Restore the project and its tasks.
     *
     * @return bool
     */
    public function restore(): bool
    {
        $this->tasks()->restore();

        return $this->parentRestore();
    }
}
