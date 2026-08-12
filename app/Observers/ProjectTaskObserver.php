<?php

namespace App\Observers;

use App\Models\ProjectTask;

class ProjectTaskObserver
{
    /**
     * Handle the ProjectTask "created" event.
     */
    public function created(ProjectTask $task): void
    {
        $this->recalculate($task);
    }

    /**
     * Handle the ProjectTask "updated" event.
     */
    public function updated(ProjectTask $task): void
    {
        $this->recalculate($task);
    }

    /**
     * Handle the ProjectTask "deleted" event.
     */
    public function deleted(ProjectTask $task): void
    {
        $this->recalculate($task);
    }

    /**
     * Handle the ProjectTask "restored" event.
     */
    public function restored(ProjectTask $task): void
    {
        $this->recalculate($task);
    }

    /**
     * Recalculate status of the parent phase and overall project.
     */
    protected function recalculate(ProjectTask $task): void
    {
        $phase = $task->phase;
        if ($phase) {
            $phase->recalculateStatus();

            $project = $phase->project;
            if ($project) {
                $project->recalculateProgress();
            }
        }
    }
}
