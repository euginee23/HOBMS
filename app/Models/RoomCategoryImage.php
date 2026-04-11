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
        return Attribute::get(fn (): string => route('media.public', ['path' => ltrim($this->image_path, '/')]));
    }
}
