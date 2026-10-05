<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name',
        'site_tagline',
        'site_address',
        'whatsapp_number',
        'contact_email',
        'logo_path',
        'favicon_path',
        'instagram_url',
        'tiktok_url',
        'meta_description',
    ];

    /**
     * Helper Static untuk mengambil setting tunggal (Singleton pattern)
     */
    public static function getSettings()
    {
        return self::firstOrCreate([], [
            'site_name' => 'Matcha Store',
            'site_tagline' => 'Matcha Premium Autentik',
            'whatsapp_number' => '6281234567890'
        ]);
    }
}