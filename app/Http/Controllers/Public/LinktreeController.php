<?php 
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\LinktreeItem;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class LinktreeController extends Controller
{
    public function index()
    {
        // 1. Ambil Data Identitas Toko
        $siteSettings = SiteSetting::first();

        // 2. Ambil Linktree Menu (Parent yang aktif + Sub-menu), diurutkan berdasarkan sort_order
        $links = LinktreeItem::with(['children' => function ($query) {
                        $query->where('is_active', true)->orderBy('sort_order', 'asc');
                    }])
                    ->whereNull('parent_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order', 'asc')
                    ->get();

        // 3. Ambil Banner Promo Aktif (Jika ada)
        $promoBanner = LinktreeItem::where('is_promo_banner', true)
                               ->where('is_active', true)
                               ->first();

        return view('public.linktree', compact('siteSettings', 'links', 'promoBanner'));
    }
}