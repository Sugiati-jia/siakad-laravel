<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Show the form to request a password reset link.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate 6-digit OTP
        $otp = sprintf('%06d', mt_rand(100000, 999999));
        
        \DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($otp), 'created_at' => now()]
        );

        // Send email
        try {
            Mail::send('auth.emails.reset-password', ['token' => $otp, 'email' => $user->email], function ($m) use ($user) {
                $m->to($user->email)->subject('Kode OTP Reset Password SIAKAD');
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }

        // Store email in session to verify OTP later
        session(['reset_email' => $user->email]);

        return redirect()->route('password.otp')->with('success', 'Kode OTP telah dikirim ke email Anda.');
    }

    public function showOtpForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request')->with('error', 'Silakan masukkan email Anda terlebih dahulu.');
        }
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $email = session('reset_email');
        if (!$email) {
            return redirect()->route('password.request')->with('error', 'Sesi telah habis. Silakan ulangi proses.');
        }

        $record = \DB::table('password_resets')->where('email', $email)->first();
        if (!$record || !Hash::check($request->otp, $record->token)) {
            return back()->with('error', 'Kode OTP tidak valid.');
        }

        // OTP is valid. Store token in session so we can reset password.
        session(['reset_token' => $request->otp]);

        return redirect()->route('password.reset.form')->with('success', 'OTP valid. Silakan buat kata sandi baru.');
    }

    /**
     * Show the password reset form.
     */
    public function showResetForm()
    {
        $token = session('reset_token');
        if (!$token) {
            return redirect()->route('password.request')->with('error', 'Silakan verifikasi OTP terlebih dahulu.');
        }
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Handle the actual password reset.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:6',
            'token' => 'required',
        ]);

        $record = \DB::table('password_resets')->where('email', $request->email)->first();
        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->with('error', 'Token/OTP tidak valid atau kedaluwarsa.');
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the token record
        \DB::table('password_resets')->where('email', $request->email)->delete();
        
        // Clear session
        session()->forget(['reset_email', 'reset_token']);

        return redirect()->route('login')->with('success', 'Kata sandi berhasil direset.');
    }
}
?>
