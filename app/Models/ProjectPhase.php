<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectPhase extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'project_id',
        'name',
        'sort_order',
        'status',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * A phase belongs to a project.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * A phase has many tasks.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class, 'phase_id');
    }

    // -------------------------------------------------------------------------
    // Business Logic
    // -------------------------------------------------------------------------

    /**
     * Recalculate status based on internal tasks state:
     * - 100% completed tasks => 'completed'
     * - 0 completed tasks (or no tasks) => 'pending'
     * - Some completed tasks => 'in_progress'
     */
    public function recalculateStatus(): void
    {
        $stats = $this->tasks()
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) as completed')
            ->first();

        $total = (int) ($stats->total ?? 0);
        $completed = (int) ($stats->completed ?? 0);

        if ($total === 0) {
            $newStatus = 'pending';
        } elseif ($completed === $total) {
            $newStatus = 'completed';
        } elseif ($completed > 0) {
            $newStatus = 'in_progress';
        } else {
            $newStatus = 'pending';
        }

        if ($this->status !== $newStatus) {
            $this->update(['status' => $newStatus]);
        }
    }
}
