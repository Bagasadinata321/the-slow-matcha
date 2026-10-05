<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    public $timestamps = false;
    protected $fillable = ['filename', 'original_filename', 'path', 'mime_type', 'file_size', 'created_at'];
}
