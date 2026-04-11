<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomCategoryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_category_id',
        'image_path',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function roomCategory(): BelongsTo
    {
        return $this->belongsTo(RoomCategory::class);
    }

    /**
     * Get the image URL.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            $path = $this->normalizePublicImagePath($this->image_path);

            if (! $path) {
                return null;
            }

            return route('media.public', ['path' => $path], false);
        });
    }

    protected function normalizePublicImagePath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $normalizedPath = trim($path);

        if (filter_var($normalizedPath, FILTER_VALIDATE_URL)) {
            $parsedPath = parse_url($normalizedPath, PHP_URL_PATH);
            $normalizedPath = is_string($parsedPath) ? $parsedPath : '';
        }

        $normalizedPath = ltrim($normalizedPath, '/');

        if (str_starts_with($normalizedPath, 'storage/')) {
            $normalizedPath = substr($normalizedPath, strlen('storage/'));
        }

        if (str_starts_with($normalizedPath, 'media/')) {
            $normalizedPath = substr($normalizedPath, strlen('media/'));
        }

        return $normalizedPath !== '' ? $normalizedPath : null;
    }
}
