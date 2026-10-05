<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaRelation extends Model
{
    public $timestamps = false;
    protected $fillable = ['media_id', 'entity_type', 'entity_id', 'usage'];

    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}
