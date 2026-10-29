<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $gurus = \App\Models\Guru::orderBy('nama')->get();
        return view('admin.setting.index', compact('settings', 'gurus'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string',
            'alamat_sekolah' => 'required|string',
            'kepala_sekolah_id' => 'required|exists:guru,id_guru',
        ]);

        Setting::updateOrCreate(['key' => 'nama_sekolah'], ['value' => $request->nama_sekolah]);
        Setting::updateOrCreate(['key' => 'alamat_sekolah'], ['value' => $request->alamat_sekolah]);
        Setting::updateOrCreate(['key' => 'kepala_sekolah_id'], ['value' => $request->kepala_sekolah_id]);

        return redirect()->route('admin.setting.index')->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
