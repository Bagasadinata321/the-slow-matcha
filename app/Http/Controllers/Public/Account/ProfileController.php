<?php

namespace App\Http\Controllers\Public\Account;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Tampilkan Halaman Edit Profil
     */
    public function edit()
{

    return view('public.account.profile', [
        'user' => Auth::user()
    ]);
}

    /**
     * Update Informasi Profil (Nama, Email, WA/Phone)
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = User::findOrFail(Auth::id());

        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
        ]);

        return redirect()->back()->with('success_profile', 'Informasi profil berhasil diperbarui.');
    }

    /**
     * Update Password
     */
    public function updatePassword(Request $request)
    {
        /** @var User $user */
        $user = User::findOrFail(Auth::id());

        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->back()->with('success_password', 'Password berhasil diperbarui.');
    }
}