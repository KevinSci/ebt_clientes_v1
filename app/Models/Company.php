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
     * Get the count of active projects.
     */
    public function activeProjectsCount(): int
    {
        return $this->projects()->active()->count();
    }

    /**
     * Get the latest modification date across company, its projects and posts.
     */
    public function lastModifiedDate()
    {
        $dates = collect([$this->updated_at]);

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

        return $dates->filter()->max();
    }
}
