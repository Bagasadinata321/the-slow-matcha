<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LinktreeItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'parent_id',
        'label',
        'has_sub',
        'url',
        'icon',
        'sort_order',
        'is_active',
    ];

    // Self-referencing: Mengambil sub-menu di bawah item ini (ditambahkan orderBy)
    public function children(): HasMany
    {
        return $this->hasMany(LinktreeItem::class, 'parent_id')->orderBy('sort_order', 'asc');
    }

    // Self-referencing: Mengambil menu induk dari item ini
    public function parent(): BelongsTo
    {
        return $this->belongsTo(LinktreeItem::class, 'parent_id');
    }
}