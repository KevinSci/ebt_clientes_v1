<?php

use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\ProjectController as ClientProjectController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StorageController;

// ─────────────────────────────────────────────────────────────────────────────
// Root redirect
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.companies.index')
            : redirect()->route('client.dashboard');
    }
    return redirect()->route('login');
});

// ─────────────────────────────────────────────────────────────────────────────
// Authentication routes (guests only)
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ─────────────────────────────────────────────────────────────────────────────
// Admin panel — requires authentication and admin role
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Companies (index + show + store/create via modal + update + destroy)
        Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
        Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
        Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
        Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
        Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');

        // Users
        Route::resource('users', UserController::class)->except(['show']);

        // Projects (show, store, update, destroy a company's project)
        Route::get('/companies/{company}/projects/{project}', [AdminProjectController::class, 'show'])
            ->name('companies.projects.show');
        Route::post('/companies/{company}/projects', [AdminProjectController::class, 'store'])
            ->name('companies.projects.store');
        Route::put('/companies/{company}/projects/{project}', [AdminProjectController::class, 'update'])
            ->name('companies.projects.update');
        Route::delete('/companies/{company}/projects/{project}', [AdminProjectController::class, 'destroy'])
            ->name('companies.projects.destroy');

        // Posts (create/update a post for a project)
        Route::post('/companies/{company}/projects/{project}/posts', [PostController::class, 'store'])
            ->name('companies.projects.posts.store');
        Route::put('/companies/{company}/projects/{project}/posts/{post}', [PostController::class, 'update'])
            ->name('companies.projects.posts.update');
        Route::delete('/companies/{company}/projects/{project}/posts/{post}', [PostController::class, 'destroy'])
            ->name('companies.projects.posts.destroy');

        // Profile / Settings
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });

// ─────────────────────────────────────────────────────────────────────────────
// Client panel — requires authentication and client role
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:client'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {

        // Dashboard (selection or redirect)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Scoped company routes with check middleware
        Route::middleware('company.access')->group(function () {
            Route::get('/companies/{company}/projects', [ClientProjectController::class, 'index'])
                ->name('companies.projects.index');
            Route::get('/companies/{company}/projects/{project}', [ClientProjectController::class, 'show'])
                ->name('companies.projects.show');
        });
    });

// ─────────────────────────────────────────────────────────────────────────────
// Storage Fallback (Hostinger / Broken Symlink bypass)
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/storage/{path}', [StorageController::class, 'show'])
        ->where('path', '.*')
        ->name('storage.show');
});
