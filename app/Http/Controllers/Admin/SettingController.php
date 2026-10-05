<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Tampilkan Halaman Site Settings
     */
    public function index()
    {
        $settings = SiteSetting::first();
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update Data Site Settings
     */
    public function updateSite(Request $request)
    {
        $setting = SiteSetting::first() ?? new SiteSetting();

        $data = $request->validate([
            'site_name'        => 'required|string|max:255',
            'site_tagline'     => 'nullable|string|max:255',
            'site_description' => 'nullable|string',
            'contact_email'    => 'nullable|email',
            'contact_phone'    => 'nullable|string|max:20',
            'address'          => 'nullable|string',
            'instagram_url'    => 'nullable|url',
            'tiktok_url'       => 'nullable|url',
            
            // Validasi Hero Section & Video
            'hero_title'       => 'nullable|string|max:255',
            'hero_subtitle'    => 'nullable|string',
            'hero_video_url'   => 'nullable|url',
            'hero_video'       => 'nullable|mimes:mp4,webm,ogg|max:20480', // Maksimal 20MB
            
            // Validasi Media
            'logo'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'favicon'          => 'nullable|image|mimes:ico,png,svg|max:1024',
        ]);

        // Upload Logo
        if ($request->hasFile('logo')) {
            if ($setting->logo) Storage::disk('public')->delete($setting->logo);
            $data['logo'] = $request->file('logo')->store('settings', 'public');
        }

        // Upload Favicon
        if ($request->hasFile('favicon')) {
            if ($setting->favicon) Storage::disk('public')->delete($setting->favicon);
            $data['favicon'] = $request->file('favicon')->store('settings', 'public');
        }

        // Upload Video Hero Background
        if ($request->hasFile('hero_video')) {
            if ($setting->hero_video_path) Storage::disk('public')->delete($setting->hero_video_path);
            $data['hero_video_path'] = $request->file('hero_video')->store('settings/hero', 'public');
        }

        $setting->fill($data)->save();

        return redirect()->back()->with('success', 'Pengaturan situs dan hero video berhasil diperbarui!');
    }
}