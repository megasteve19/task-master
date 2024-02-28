<?php

namespace App\Observers;

use App\Models\Task;

class TaskObserver
{
    public function creating(Task $task)
    {
		// You may ask why I'm not just using `autoincrement` for the priority field.
		// Laravel is great, but sometimes it can be odd.
		// Apparently, on the migration, we can only define the field as `autoincrement` if it's the primary key.
		// So, I'm using this observer to set the priority field to the next available number.
		// Not healthy for a large dataset, but this is a small project.
        $task->priority = Task::max('priority') + 1;
    }
}
