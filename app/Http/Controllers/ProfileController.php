<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ];

        // Jika user mengisi password baru
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Upload dan simpan foto jika ada
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto && Storage::exists('public/' . $user->foto)) {
                Storage::delete('public/' . $user->foto);
            }
            $data['foto'] = $request->file('foto')->store('profile_photos', 'public');
        }

        $user->update($data);

        // Jika guru, update nama di tabel guru juga
        if ($user->role == 'guru' || $user->role == 'wali_kelas') {
            if ($user->guru) {
                $user->guru->update(['nama' => $request->name]);
            }
        }

        return back()->with('success', 'Profil berhasil diperbarui');
    }
}
