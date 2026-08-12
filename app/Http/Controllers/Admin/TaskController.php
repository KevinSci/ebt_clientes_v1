<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ProjectTask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Store a newly created task in a phase.
     */
    public function store(Request $request, Company $company, Project $project, ProjectPhase $phase): RedirectResponse
    {
        abort_if($project->company_id !== $company->id, 404);
        abort_if($phase->project_id !== $project->id, 404);

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $maxOrder = $phase->tasks()->max('sort_order') ?? 0;

        $task = $phase->tasks()->create([
            'name'         => $validated['name'],
            'sort_order'   => $validated['sort_order'] ?? ($maxOrder + 1),
            'is_completed' => false,
        ]);

        return redirect()
            ->route('admin.companies.projects.show', [$company, $project])
            ->with('success', 'Tarea creada correctamente.');
    }

    /**
     * Toggle task completed status (restricted to admin).
     * Inverse workflow: completing or unchecking task automatically updates phase & project status.
     */
    public function toggleComplete(Request $request, Company $company, Project $project, ProjectPhase $phase, ProjectTask $task): RedirectResponse
    {
        abort_if($project->company_id !== $company->id, 404);
        abort_if($phase->project_id !== $project->id, 404);
        abort_if($task->phase_id !== $phase->id, 404);

        $isCompleted = $request->boolean('completed', !$task->is_completed);

        $task->setCompleted($isCompleted);

        // Observer automatically recalculates phase status & project progress

        $message = $isCompleted ? 'Tarea marcada como completada.' : 'Tarea desmarcada.';

        return redirect()
            ->route('admin.companies.projects.show', [$company, $project])
            ->with('success', $message);
    }

    /**
     * Delete the specified task (restricted to admin).
     */
    public function destroy(Company $company, Project $project, ProjectPhase $phase, ProjectTask $task): RedirectResponse
    {
        abort_if($project->company_id !== $company->id, 404);
        abort_if($phase->project_id !== $project->id, 404);
        abort_if($task->phase_id !== $phase->id, 404);

        $task->delete();

        // Observer automatically recalculates phase status & project progress

        return redirect()
            ->route('admin.companies.projects.show', [$company, $project])
            ->with('success', 'Tarea eliminada correctamente.');
    }
}
