<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Client\ProjectController as ClientProjectController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────────────────────
// Root redirect
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.clients.index')
            : redirect()->route('client.projects.index');
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

        // Clients (index + show + store/create via modal + update + destroy)
        Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
        Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

        // Projects (show, store, destroy a client's project)
        Route::get('/clients/{client}/projects/{project}', [AdminProjectController::class, 'show'])
            ->name('clients.projects.show');
        Route::post('/clients/{client}/projects', [AdminProjectController::class, 'store'])
            ->name('clients.projects.store');
        Route::delete('/clients/{client}/projects/{project}', [AdminProjectController::class, 'destroy'])
            ->name('clients.projects.destroy');

        // Posts (create a post for a project)
        Route::post('/clients/{client}/projects/{project}/posts', [PostController::class, 'store'])
            ->name('clients.projects.posts.store');
    });

// ─────────────────────────────────────────────────────────────────────────────
// Client panel — requires authentication and client role
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:client'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {

        // Projects list and feed
        Route::get('/projects', [ClientProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{project}', [ClientProjectController::class, 'show'])->name('projects.show');
    });
