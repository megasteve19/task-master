<?php

namespace App\Models;

use App\Enums\TaskStatus;
use App\Observers\TaskObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

#[ObservedBy(TaskObserver::class)]
class Task extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUlids;
    use Searchable;

    protected $fillable = [
        'name',
        'description',
        'status',
        'priority',
        'archived_at',
        'due_date',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
        'due_date' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status->prettyName(),
            'project' => $this->project->name,
            'assignees' => $this->assignees->pluck('name')->toArray(),
            'due_date' => $this->due_date?->format('Y-m-d'),
            'created_at' => $this->created_at->timestamp,
        ];
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param Builder $builder
     */
    protected function makeAllSearchableUsing(Builder $builder): Builder
    {
        return $builder->with('assignees');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'assignee_task');
    }

    public function scopeOverdue(Builder $builder)
    {
        $builder->where('due_date', '<', now());
    }

    public function scopeAssignedTo(Builder $builder, User|string ...$users)
    {
        $users = collect($users)->map(fn ($user) => $user instanceof User ? $user->id : $user);

        return $builder->whereHas('assignees', function(Builder $builder) use ($users) {
            $builder->whereIn('user_id', $users);
        });
    }

    public function scopeActive(Builder $builder)
    {
        $builder->whereNull('archived_at');
    }

    public function scopeArchived(Builder $builder)
    {
        $builder->whereNotNull('archived_at');
    }

    public function scopeCompleted(Builder $builder)
    {
        $builder->where('status', TaskStatus::Completed);
    }

    public function archive()
    {
        $this->update(['archived_at' => now()]);
    }

    public function restoreFromArchive()
    {
        $this->update(['archived_at' => null]);
    }
}
