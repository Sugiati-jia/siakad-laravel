<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'checkRole' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Convert ModelNotFoundException to 404 for all users
        $exceptions->renderable(function (ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yang diminta tidak ditemukan.',
                ], 404);
            }
            abort(404, 'Data yang diminta tidak ditemukan.');
        });

        $exceptions->renderable(function (QueryException $e, Request $request) {
            // Always log to laravel.log so admin can see it
            \Illuminate\Support\Facades\Log::error('Database Error: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => auth()->id(),
                'url' => $request->fullUrl(),
                'input' => $request->except(['password', '_token']),
            ]);

            $friendlyMessage = 'Format nilai tidak valid. Pastikan format dan isian data sudah benar.';
            $errorCode = 422;

            if (stripos($e->getMessage(), 'Incorrect integer value') !== false) {
                $friendlyMessage = 'Penulisan Kode TP atau Nilai salah! Harus berupa angka (contoh: 1, 2, bukan "TP 1").';
            } elseif (stripos($e->getMessage(), 'Duplicate entry') !== false) {
                $errorCode = 409;
                $friendlyMessage = 'Nilai sudah pernah diinput atau data tersebut sudah ada.';
            } elseif (stripos($e->getMessage(), 'Data truncated') !== false) {
                $friendlyMessage = 'Isian terlalu panjang atau tidak sesuai format.';
            }

            // For AJAX/JSON requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $friendlyMessage,
                ], $errorCode);
            }

            // For superadmin: let Laravel show the default debug page
            $user = auth()->user();
            if ($user && $user->role === 'superadmin') {
                return null; // Let default handler process it
            }

            // For all other users: show sweet alert error page without leaving
            if ($request->isMethod('GET')) {
                // Jangan abort() karena akan tampilkan halaman error 422 kosong ke pengguna.
                // Lebih baik redirect ke halaman sebelumnya atau dashboard dengan pesan error.
                if (url()->previous() && url()->previous() !== $request->fullUrl()) {
                    return redirect()->back()->with('sweet_error', $friendlyMessage)->with('sweet_error_code', $errorCode);
                }
                // Jika tidak ada halaman sebelumnya (mis. buka /login langsung), arahkan ke home
                return redirect()->route('login')->with('error', 'Terjadi kesalahan sistem: ' . $friendlyMessage);
            }
            return back()->withInput()->with('sweet_error', $friendlyMessage)->with('sweet_error_code', $errorCode);
        });

    })->create();

