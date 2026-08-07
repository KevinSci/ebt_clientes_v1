<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Display the authenticated client's projects.
     *
     * Projects are split into active and historical (completed/paused)
     * for the card-based mobile-first layout.
     */
    public function index(Company $company): View
    {
        $company->load('users:id,name,role');

        $activeProjects = $company->projects()
            ->active()
            ->withCount('posts')
            ->latest()
            ->get();
        $activeProjects->each(fn ($p) => $p->setRelation('company', $company));

        $historicalProjects = $company->projects()
            ->whereIn('status', ['completed', 'paused'])
            ->withCount('posts')
            ->latest()
            ->get();
        $historicalProjects->each(fn ($p) => $p->setRelation('company', $company));

        return view('client.projects.index', compact('company', 'activeProjects', 'historicalProjects'));
    }

    /**
     * Display a project's post feed with optional filters.
     *
     * Filtering is handled server-side via GET query parameters:
     * - `search` : filter by post title (LIKE)
     * - `date_from` / `date_to` : filter by published_at range
     *
     * Posts are ordered by published_at descending.
     */
    public function show(Request $request, Company $company, Project $project): View
    {
        $search   = $request->string('search')->trim();
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');
        $authorId = $request->input('author_id');

        $postsQuery = $project->posts()
            ->with([
                'attachments:id,post_id,file_name,file_path,type,folder_name,folder_path',
                'author:id,name,role',
            ])
            ->when($search->isNotEmpty(), fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->when($dateFrom, fn ($q) => $q->whereDate('published_at', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->whereDate('published_at', '<=', $dateTo))
            ->when($authorId, function ($q) use ($authorId) {
                if ($authorId === 'admin') {
                    $q->where(function ($sq) {
                        $sq->whereNull('user_id')
                           ->orWhereHas('author', fn($aq) => $aq->where('role', 'admin'));
                    });
                } else {
                    $q->where('user_id', $authorId);
                }
            })
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc');

        $canPublish = auth()->user()->canPublish();
        if ($canPublish) {
            $posts = $postsQuery->get();
        } else {
            $posts = $postsQuery->paginate(10)->withQueryString();
        }

        // Get unique client authors who have published in this project
        $authorIds = $posts instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? $project->posts()->whereNotNull('user_id')->pluck('user_id')->unique()
            : $posts->pluck('user_id')->filter()->unique();

        $authors = \App\Models\User::whereIn('id', $authorIds)
            ->where('role', 'client')
            ->select('id', 'name')
            ->get();

        return view('client.projects.show', compact('company', 'project', 'posts', 'search', 'dateFrom', 'dateTo', 'authorId', 'authors'));
    }
}
