<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LinktreeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LinktreeController extends Controller
{
    /**
     * Menampilkan halaman pengelolaan Linktree
     */
    public function index()
    {
        // Mengambil parent menu beserta children-nya yang otomatis terurut
        $linktreeItems = LinktreeItem::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            }])
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('admin.linktree.index', compact('linktreeItems'));
    }

    /**
     * Menambah Menu Utama baru (Quick Add dari panel kiri)
     */
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
        ]);

        // Dapatkan sort_order tertinggi untuk menu utama
        $maxOrder = LinktreeItem::whereNull('parent_id')->max('sort_order') ?? 0;

        LinktreeItem::create([
            'parent_id'  => null,
            'label'      => $request->label,
            'url'        => 'https://',
            'icon'       => 'link',
            'has_sub'    => false,
            'sort_order' => $maxOrder + 1,
            'is_active'  => true,
        ]);

        return redirect()->back()->with('success', 'Menu utama berhasil ditambahkan!');
    }

    /**
     * Menyimpan seluruh perubahan data (Batch Update Menu Utama & Sub-Menu)
     */
    public function updateAll(Request $request)
    {
        // Validasi opsional
        $request->validate([
            'items'            => 'nullable|array',
            'subs'             => 'nullable|array',
            'new_subs'         => 'nullable|array',
            'items.*.label'    => 'required_with:items|string|max:255',
            'subs.*.label'     => 'required_with:subs|string|max:255',
            'new_subs.*.label' => 'required_with:new_subs|string|max:255',
        ]);

        DB::transaction(function () use ($request) {

            // 1. Update Parent / Item Menu Utama
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $id => $data) {
                    $item = LinktreeItem::find($id);
                    if ($item) {
                        $hasSub = isset($data['has_sub']) && ($data['has_sub'] == '1' || $data['has_sub'] == 1);

                        $item->update([
                            'label'   => $data['label'],
                            'icon'    => $data['icon'] ?? 'link',
                            'has_sub' => $hasSub,
                            // Jika dijadikan parent, URL dikosongkan/null
                            'url'     => $hasSub ? null : ($data['url'] ?? null),
                        ]);
                    }
                }
            }

            // 2. Update Sub-Menu Yang Sudah Ada
            if ($request->has('subs') && is_array($request->subs)) {
                foreach ($request->subs as $subId => $subData) {
                    $sub = LinktreeItem::find($subId);
                    if ($sub) {
                        $sub->update([
                            'label' => $subData['label'],
                            'icon'  => $subData['icon'] ?? 'link',
                            'url'   => $subData['url'] ?? 'https://',
                        ]);
                    }
                }
            }

            // 3. Simpan Sub-Menu BARU yang ditambahkan secara dinamis
            if ($request->has('new_subs') && is_array($request->new_subs)) {
                foreach ($request->new_subs as $newSubData) {
                    if (!empty($newSubData['label']) && !empty($newSubData['parent_id'])) {
                        $parentId = $newSubData['parent_id'];

                        // Dapatkan sort_order tertinggi untuk sub-menu di bawah parent ini
                        $maxSubOrder = LinktreeItem::where('parent_id', $parentId)->max('sort_order') ?? 0;

                        LinktreeItem::create([
                            'parent_id'  => $parentId,
                            'label'      => $newSubData['label'],
                            'icon'       => $newSubData['icon'] ?? 'link',
                            'url'        => $newSubData['url'] ?? 'https://',
                            'has_sub'    => false,
                            'sort_order' => $maxSubOrder + 1,
                            'is_active'  => true,
                        ]);

                        // Pastikan status has_sub pada parent bernilai true
                        LinktreeItem::where('id', $parentId)->update(['has_sub' => true]);
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Seluruh perubahan menu & sub-menu berhasil disimpan!');
    }

    /**
     * Memperbarui urutan menu via AJAX (Sortable Drag & Drop)
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order'   => 'required|array',
            'order.*' => 'exists:linktree_items,id', // sesuaikan dengan nama tabel di database Anda
        ]);

        foreach ($request->order as $index => $id) {
            LinktreeItem::where('id', $id)->update([
                'sort_order' => $index + 1,
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Urutan menu berhasil diperbarui!',
        ]);
    }

    /**
     * Menghapus menu/sub-menu beserta anak menu yang ada di bawahnya
     */
    public function destroy($id)
    {
        $item = LinktreeItem::findOrFail($id);

        DB::transaction(function () use ($item) {
            // Hapus semua anak menu jika ada
            LinktreeItem::where('parent_id', $item->id)->delete();
            
            // Hapus item itu sendiri
            $item->delete();
        });

        return redirect()->back()->with('success', 'Menu berhasil dihapus!');
    }
}