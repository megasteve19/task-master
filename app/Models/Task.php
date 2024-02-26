<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUlids;

    protected $fillable = [
        'name',
        'description',
        'status',
        'is_archived',
        'due_date',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
        'due_date' => 'datetime',
        'is_archived' => 'boolean',
    ];

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

    public function scopeAssignedTo(Builder $builder, User $user)
    {
        $builder
            ->whereHas('assignees', function(Builder $builder) use ($user) {
                $builder->where('user_id', $user->id);
            });
            // ->whereHas('project', function(Builder $builder) use ($user) {
            //     $builder->whereHas('assignees', function(Builder $builder) use ($user) {
            //         $builder->where('user_id', $user->id);
            //     });
            // });
    }
}
