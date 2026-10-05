<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['title', 
        'slug', 
        'product_type', 
        'excerpt', 
        'content', 
        'details',        // <--- Tambahkan ini
        'serving_guide',  // <--- Tambahkan ini
        'status', 
        'sort_order'
    ];

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    // Relasi ke media_relations
    public function mediaRelations()
    {
        return $this->hasMany(MediaRelation::class, 'entity_id')
            ->where('entity_type', 'product');
    }

    // Helper mengambil media cover
    public function coverMedia()
    {
        return $this->hasOneThrough(
            Media::class,
            MediaRelation::class,
            'entity_id',
            'id',
            'id',
            'media_id'
        )->where('media_relations.entity_type', 'product')
            ->where('media_relations.usage', 'cover');
    }

    // Helper mengambil media galeri
    public function galleryMedia()
    {
        return $this->hasManyThrough(
            Media::class,
            MediaRelation::class,
            'entity_id',
            'id',
            'id',
            'media_id'
        )->where('media_relations.entity_type', 'product')
            ->where('media_relations.usage', 'gallery');
    }
    // Relasi ke tabel reviews
    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    // Helper untuk menghitung rata-rata bintang (contoh: 4.8)
    public function averageRating()
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    // Helper untuk total ulasan
    public function totalReviews()
    {
        return $this->reviews()->count();
    }
}
