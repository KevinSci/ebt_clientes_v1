<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the client dashboard entry.
     *
     * Evaluates the number of companies the client belongs to:
     * - 1 company  → auto-redirect to company projects
     * - >1 company → render company selector dashboard
     * - 0 companies → show page with notice/alert
     */
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();
        $companies = $user->companies;

        if ($companies->count() === 1) {
            return redirect()->route('client.companies.projects.index', $companies->first());
        }

        return view('client.companies.index', compact('companies'));
    }
}
