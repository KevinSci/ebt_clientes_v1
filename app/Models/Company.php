<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'rfc',
        'address',
        'phone',
        'tax_regime',
    ];

    /**
     * Get the users that belong to this company.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Get the projects for the company.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Get active projects count attribute.
     */
    public function getActiveProjectsCountAttribute(): int
    {
        if (isset($this->attributes['active_projects_count'])) {
            return (int) $this->attributes['active_projects_count'];
        }

        if ($this->relationLoaded('projects')) {
            return $this->projects->where('status', 'active')->count();
        }

        return $this->projects()->active()->count();
    }

    /**
     * Get the count of active projects (helper / backward compatibility).
     */
    public function activeProjectsCount(): int
    {
        return $this->active_projects_count;
    }

    /**
     * Get the latest modification date attribute.
     */
    public function getLastModifiedDateAttribute()
    {
        $dates = collect([$this->updated_at]);

        if ($this->relationLoaded('projects')) {
            $dates = $dates->merge($this->projects->pluck('updated_at'));
            $this->projects->each(function ($project) use (&$dates) {
                if ($project->relationLoaded('posts')) {
                    $dates = $dates->merge($project->posts->pluck('updated_at'));
                }
            });
        } else {
            $latestProject = $this->projects()->latest('updated_at')->first();
            if ($latestProject) {
                $dates->push($latestProject->updated_at);

                $latestPost = \App\Models\Post::whereIn('project_id', $this->projects()->pluck('id'))
                    ->latest('updated_at')
                    ->first();
                if ($latestPost) {
                    $dates->push($latestPost->updated_at);
                }
            }
        }

        return $dates->filter()->max();
    }

    /**
     * Get the latest modification date (helper / backward compatibility).
     */
    public function lastModifiedDate()
    {
        return $this->last_modified_date;
    }
}
