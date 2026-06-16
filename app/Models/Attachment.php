<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'post_id',
        'file_name',
        'file_path',
        'type',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * An attachment belongs to a post.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    // -------------------------------------------------------------------------
    // Helper Methods
    // -------------------------------------------------------------------------

    /**
     * Determine if this attachment is an image.
     */
    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    /**
     * Determine if this attachment is a document.
     */
    public function isDocument(): bool
    {
        return $this->type === 'document';
    }

    /**
     * Determine if this attachment is a PDF.
     */
    public function isPdf(): bool
    {
        return strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION)) === 'pdf';
    }

    /**
     * Get the full public URL for this attachment.
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Get the Bootstrap Icon class and color class based on the file extension.
     *
     * @return array{icon: string, color: string}
     */
    public function getIconAttribute(): array
    {
        $extension = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));

        return match ($extension) {
            'pdf' => [
                'icon' => 'bi-file-earmark-pdf-fill',
                'color' => 'text-danger'
            ],
            'doc', 'docx' => [
                'icon' => 'bi-file-earmark-word-fill',
                'color' => 'text-primary'
            ],
            'xls', 'xlsx' => [
                'icon' => 'bi-file-earmark-excel-fill',
                'color' => 'text-success'
            ],
            'zip', 'rar' => [
                'icon' => 'bi-file-earmark-zip-fill',
                'color' => 'text-warning'
            ],
            default => [
                'icon' => 'bi-file-earmark-arrow-down-fill',
                'color' => 'text-secondary'
            ],
        };
    }
}
