<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'status',
        'progress_mode',
        'progress_percentage',
        'created_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'progress_percentage' => 'integer',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * A project belongs to a company.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * A project has many posts.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * A project has many phases.
     */
    public function phases(): HasMany
    {
        return $this->hasMany(ProjectPhase::class);
    }

    /**
     * A project has many tasks through phases.
     */
    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(ProjectTask::class, ProjectPhase::class, 'project_id', 'phase_id');
    }

    // -------------------------------------------------------------------------
    // Business Logic & Helpers
    // -------------------------------------------------------------------------

    public function isManualProgress(): bool
    {
        return $this->progress_mode === 'manual';
    }

    public function isPhasesProgress(): bool
    {
        return $this->progress_mode === 'phases';
    }

    /**
     * Recalculate and persist progress percentage and status when in phases mode.
     */
    public function recalculateProgress(): void
    {
        if ($this->isManualProgress()) {
            return;
        }

        $stats = $this->tasks()
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN project_tasks.is_completed = 1 THEN 1 ELSE 0 END) as completed')
            ->first();

        $totalTasks = (int) ($stats->total ?? 0);
        $completedTasks = (int) ($stats->completed ?? 0);

        if ($totalTasks === 0) {
            $percentage = 0;
            $newStatus = ($this->status === 'completed') ? 'active' : $this->status;
        } else {
            $percentage = (int) round(($completedTasks / $totalTasks) * 100);

            // Project status rules:
            // 100% of tasks completed -> 'completed'
            // Otherwise if status was 'completed', reopen to 'active'
            if ($completedTasks === $totalTasks) {
                $newStatus = 'completed';
            } else {
                $newStatus = ($this->status === 'completed') ? 'active' : $this->status;
            }
        }

        if ($this->progress_percentage !== $percentage || $this->status !== $newStatus) {
            $this->update([
                'progress_percentage' => $percentage,
                'status'              => $newStatus,
            ]);
        }
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Scope to only return active projects.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', 'active');
    }

    /**
     * Scope to only return completed projects.
     */
    public function scopeCompleted(Builder $query): void
    {
        $query->where('status', 'completed');
    }
}
