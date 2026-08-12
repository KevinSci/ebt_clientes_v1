<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Store a newly created project in the database for the given company.
     */
    public function store(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'status'              => ['required', 'string', 'in:active,paused,completed'],
            'progress_mode'       => ['required', 'string', 'in:manual,phases'],
            'progress_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'created_at'          => ['nullable', 'date'],
        ]);

        $progressMode = $validated['progress_mode'];
        $progressPercentage = ($progressMode === 'phases') ? 0 : ($validated['progress_percentage'] ?? 0);

        $company->projects()->create([
            'name'                => $validated['name'],
            'status'              => $validated['status'],
            'progress_mode'       => $progressMode,
            'progress_percentage' => $progressPercentage,
            'created_at'          => $validated['created_at'] ?? now(),
        ]);

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('success', 'Proyecto creado correctamente.');
    }

    /**
     * Remove the specified project from the database.
     */
    public function destroy(Company $company, Project $project): RedirectResponse
    {
        abort_if($project->company_id !== $company->id, 404);

        $project->delete();

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('success', 'Proyecto eliminado correctamente.');
    }

    /**
     * Display a specific project with its posts, attachments, phases and tasks.
     *
     * The company ownership is verified to ensure data isolation.
     */
    public function show(Company $company, Project $project): View
    {
        abort_if($project->company_id !== $company->id, 404);

        $project->load([
            'phases' => fn ($q) => $q->orderBy('sort_order')
                ->withCount([
                    'tasks',
                    'tasks as completed_tasks_count' => fn ($t) => $t->where('is_completed', true),
                ]),
            'phases.tasks' => fn ($q) => $q->orderBy('sort_order'),
            'posts' => fn ($q) => $q->with([
                'attachments:id,post_id,file_name,file_path,type,folder_name,folder_path',
                'author:id,name,role',
                'task:id,phase_id,name',
            ])->latest('published_at')->latest('id'),
        ]);

        return view('admin.projects.show', compact('company', 'project'));
    }

    /**
     * Update the specified project in the database.
     */
    public function update(Request $request, Company $company, Project $project): RedirectResponse
    {
        abort_if($project->company_id !== $company->id, 404);

        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'status'              => ['required', 'string', 'in:active,paused,completed'],
            'progress_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'created_at'          => ['nullable', 'date'],
        ]);

        $updateData = [
            'name'       => $validated['name'],
            'status'     => $validated['status'],
            'created_at' => $validated['created_at'] ?? $project->created_at,
        ];

        // Progress mode is immutable! If project is manual, update progress_percentage manually.
        if ($project->isManualProgress()) {
            $updateData['progress_percentage'] = $validated['progress_percentage'] ?? 0;
        }

        $project->update($updateData);

        // If phases mode, recalculate progress automatically in case status changed
        if ($project->isPhasesProgress()) {
            $project->recalculateProgress();
        }

        return redirect()
            ->route('admin.companies.projects.show', [$company, $project])
            ->with('success', 'Proyecto actualizado correctamente.');
    }
}
