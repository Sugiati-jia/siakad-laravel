<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $userExists = \App\Models\User::where('email', $request->email)->exists();

        if (!$userExists) {
            return back()->withErrors([
                'email' => 'Pengguna tidak terverifikasi.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'Login',
                'description' => 'User logged into the application via email',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            // Redirect based on role
            return match($user->role) {
                'admin', 'superadmin' => redirect()->intended('/admin/dashboard'),
                'guru' => redirect()->intended('/guru/dashboard'),
                'wali_kelas' => redirect()->intended('/walikelas/dashboard'),
                'siswa' => redirect()->intended('/siswa/dashboard'),
                'orang_tua' => redirect()->intended('/orangtua/dashboard'),
                default => redirect()->intended('/dashboard')
            };
        }

        return back()->withErrors([
            'password' => 'Password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $role = Auth::user()?->role;
        
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        if ($role === 'orang_tua' || $role === 'siswa') {
            return redirect('/login');
        }
        
        return redirect('login');
    }

    /**
     * Display the parent login view.
     */
    public function parentLoginView(): View
    {
        return view('auth.parent-login');
    }

    /**
     * Handle parent login using child's NISN only.
     */
    public function parentLogin(Request $request): RedirectResponse
    {
        $request->validate([
            'nisn' => ['required'],
            'password' => ['required'],
            'login_as' => ['required', 'in:siswa,orang_tua'],
        ]);

        $nisn = $request->nisn;
        $password = $request->password;
        $loginAs = $request->login_as;

        // Cari siswa dengan NISN tersebut
        $siswa = \App\Models\Siswa::where('nisn', $nisn)->first();

        if (!$siswa) {
            $userSiswa = \App\Models\User::where('nisn', $nisn)->where('role', 'siswa')->first();
            if ($userSiswa) {
                $siswa = \App\Models\Siswa::where('user_id', $userSiswa->id)->first();
            }
        }

        if ($siswa) {
            // Verifikasi password (menggunakan username/nisn karena user siswa menggunakan itu)
            $user = \App\Models\User::where('username', $nisn)->orWhere('nisn', $nisn)->first();
            
            // Karena user bilang "passwordnya menggunakan nisn juga ok", 
            // kita asumsikan password valid jika sama dengan NISN atau lolos Hash::check
            $passwordValid = false;
            if ($password === $nisn) {
                $passwordValid = true;
            } elseif ($user && \Illuminate\Support\Facades\Hash::check($password, $user->password)) {
                $passwordValid = true;
            }

            if ($passwordValid) {
                if ($loginAs === 'siswa') {
                    if ($user) {
                        Auth::login($user);
                        $request->session()->regenerate();
                        return redirect()->intended('/siswa/dashboard');
                    } else {
                        return back()->withErrors(['nisn' => 'Akun user untuk Siswa belum dibuat.'])->onlyInput('nisn');
                    }
                } else {
                    // Login sebagai Orang Tua
                    $parentUser = \App\Models\User::firstOrCreate(
                        ['role' => 'orang_tua'],
                        [
                            'name' => 'Orang Tua / Wali Siswa',
                            'email' => 'ortu@smksunangiri.sch.id',
                            'password' => \Illuminate\Support\Facades\Hash::make('rahasia_sistem_123')
                        ]
                    );
                    
                    if ($parentUser) {
                        Auth::login($parentUser);
                        $request->session()->regenerate();
                        
                        session(['selected_child_id' => $siswa->id_siswa]);
                        session()->flash('trigger_erapor_notif', true);

                        return redirect()->intended('/orangtua/dashboard');
                    }
                }
            } else {
                return back()->withErrors(['password' => 'Password salah.'])->onlyInput('nisn');
            }
        }

        return back()->withErrors([
            'nisn' => 'NISN Siswa tidak ditemukan.',
        ])->onlyInput('nisn');
    }
}
