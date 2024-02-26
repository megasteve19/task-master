<?php

namespace App\Enums;

enum TaskStatus: string
{
    /**
     * Represents a task that is not yet started.
     */
    case Todo = 'todo';

    /**
     * Represents a task that is in progress.
     */
    case InProgress = 'in_progress';

    /**
     * Represents a task that has been completed.
     */
    case Completed = 'completed';

    /**
     * Returns the human-readable label for the task status.
     *
     * @return string
     */
    public function prettyName(): string
    {
        return match ($this)
        {
            TaskStatus::Todo => 'To Do',
            TaskStatus::InProgress => 'In Progress',
            TaskStatus::Completed => 'Completed',
        };
    }
}
