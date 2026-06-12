<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCompanyAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $company = $request->route('company');
        $user = auth()->user();

        if ($company) {
            // Resolve company if not already resolved by route model binding
            if (!$company instanceof \App\Models\Company) {
                $company = \App\Models\Company::find($company);
            }

            if (!$company) {
                abort(404, 'Empresa no encontrada.');
            }

            // Ensure user belongs to this company
            if (!$user || !$user->companies->contains($company->id)) {
                abort(403, 'No tienes acceso a esta empresa.');
            }

            // Also check project if present in the route
            $project = $request->route('project');
            if ($project) {
                if (!$project instanceof \App\Models\Project) {
                    $project = \App\Models\Project::find($project);
                }

                if (!$project || $project->company_id !== $company->id) {
                    abort(403, 'Este proyecto no pertenece a la empresa especificada.');
                }
            }
        }

        return $next($request);
    }
}
