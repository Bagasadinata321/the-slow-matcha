<?php

namespace App\Http\Controllers\Public\Account;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Address::where('user_id', Auth::id())
            ->orderByDesc('is_primary')
            ->latest()
            ->get();

        return view('public.account.addresses', compact('addresses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label'          => 'required|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'phone_number'   => 'required|string|max:20',
            'full_address'   => 'required|string',
            'province'       => 'nullable|string|max:100',
            'city'           => 'nullable|string|max:100',
            'district'       => 'nullable|string|max:100',
            'postal_code'    => 'nullable|string|max:10',
            'is_primary'     => 'nullable|boolean',
        ]);

        // Jika diset sebagai alamat utama, matikan status primary di alamat lain milik user
        if ($request->has('is_primary')) {
            Address::where('user_id', Auth::id())->update(['is_primary' => false]);
        }

        Address::create([
            'user_id'        => Auth::id(),
            'label'          => $validated['label'],
            'recipient_name' => $validated['recipient_name'],
            'phone_number'   => $validated['phone_number'],
            'full_address'   => $validated['full_address'],
            'province'       => $validated['province'] ?? null,
            'city'           => $validated['city'] ?? null,
            'district'       => $validated['district'] ?? null,
            'postal_code'    => $validated['postal_code'] ?? null,
            'is_primary'     => $request->has('is_primary') || Address::where('user_id', Auth::id())->count() === 0,
        ]);

        return redirect()->back()->with('success', 'Alamat baru berhasil ditambahkan.');
    }

    public function setPrimary($id)
    {
        Address::where('user_id', Auth::id())->update(['is_primary' => false]);
        Address::where('user_id', Auth::id())->where('id', $id)->update(['is_primary' => true]);

        return redirect()->back()->with('success', 'Alamat utama berhasil diubah.');
    }

    public function destroy($id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);
        $address->delete();

        return redirect()->back()->with('success', 'Alamat berhasil dihapus.');
    }
}