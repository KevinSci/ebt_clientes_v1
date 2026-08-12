<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Project;
use App\Models\ProjectPhase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PhaseController extends Controller
{
    /**
     * Store a newly created phase for a project.
     */
    public function store(Request $request, Company $company, Project $project): RedirectResponse
    {
        abort_if($project->company_id !== $company->id, 404);
        abort_if(!$project->isPhasesProgress(), 400, 'El proyecto no está configurado para fases.');

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $maxOrder = $project->phases()->max('sort_order') ?? 0;

        $project->phases()->create([
            'name'       => $validated['name'],
            'sort_order' => $validated['sort_order'] ?? ($maxOrder + 1),
            'status'     => 'pending',
        ]);

        return redirect()
            ->route('admin.companies.projects.show', [$company, $project])
            ->with('success', 'Fase creada correctamente.');
    }

    /**
     * Update the specified phase.
     */
    public function update(Request $request, Company $company, Project $project, ProjectPhase $phase): RedirectResponse
    {
        abort_if($project->company_id !== $company->id, 404);
        abort_if($phase->project_id !== $project->id, 404);

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $phase->update([
            'name'       => $validated['name'],
            'sort_order' => $validated['sort_order'] ?? $phase->sort_order,
        ]);

        return redirect()
            ->route('admin.companies.projects.show', [$company, $project])
            ->with('success', 'Fase actualizada correctamente.');
    }

    /**
     * Delete the specified phase and its tasks.
     */
    public function destroy(Company $company, Project $project, ProjectPhase $phase): RedirectResponse
    {
        abort_if($project->company_id !== $company->id, 404);
        abort_if($phase->project_id !== $project->id, 404);

        // Deleting tasks will trigger Observer to recalculate phase/project
        $phase->tasks()->delete();
        $phase->delete();

        // Recalculate project progress after phase deletion
        $project->recalculateProgress();

        return redirect()
            ->route('admin.companies.projects.show', [$company, $project])
            ->with('success', 'Fase eliminada correctamente.');
    }
}
