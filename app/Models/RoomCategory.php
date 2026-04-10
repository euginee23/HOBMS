<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

class RoomCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_per_night',
        'max_capacity',
        'room_size_sqm',
        'base_occupancy',
        'extra_person_charge',
        'amenities',
        'image_path',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_per_night' => 'decimal:2',
            'max_capacity' => 'integer',
            'room_size_sqm' => 'integer',
            'base_occupancy' => 'integer',
            'extra_person_charge' => 'decimal:2',
            'amenities' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (RoomCategory $category): void {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Get rooms in this category.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Get gallery images for this category.
     */
    public function images(): HasMany
    {
        return $this->hasMany(RoomCategoryImage::class)->orderBy('sort_order');
    }

    /**
     * Get all bookings for rooms in this category.
     */
    public function bookings(): HasManyThrough
    {
        return $this->hasManyThrough(Booking::class, Room::class);
    }

    /**
     * Scope to active categories.
     *
     * @param  Builder<RoomCategory>  $query
     * @return Builder<RoomCategory>
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
