<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Show the notifications inbox page.
     */
    public function index()
    {
        $user = Auth::user();

        // Load all user notifications with the parent notification details, newest first
        $notifications = UserNotification::with('notification')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        $unreadCount = UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        // Check if this user already has an active push subscription
        $hasSubscription = PushSubscription::where('user_id', $user->id)
            ->where('is_active', true)
            ->exists();

        return view('orangtua.notifications', compact('notifications', 'unreadCount', 'hasSubscription'));
    }

    /**
     * Save (subscribe) a PWA push subscription from the browser.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint'   => 'required|string',
            'p256dh_key' => 'nullable|string',
            'auth_token' => 'nullable|string',
            'device_name' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        // Upsert: same endpoint = update, new endpoint = insert
        PushSubscription::updateOrCreate(
            ['endpoint' => $request->endpoint],
            [
                'user_id'     => $user->id,
                'p256dh_key'  => $request->p256dh_key,
                'auth_token'  => $request->auth_token,
                'device_name' => $request->device_name,
                'is_active'   => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Perangkat berhasil didaftarkan untuk menerima notifikasi.',
        ]);
    }

    /**
     * Remove (unsubscribe) a push subscription.
     */
    public function unsubscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
        ]);

        $user = Auth::user();

        PushSubscription::where('user_id', $user->id)
            ->where('endpoint', $request->endpoint)
            ->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi telah dinonaktifkan untuk perangkat ini.',
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead($id)
    {
        $user = Auth::user();

        $notif = UserNotification::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $notif->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead()
    {
        $user = Auth::user();

        UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true, 'message' => 'Semua notifikasi telah dibaca.']);
    }

    /**
     * Delete all notifications.
     */
    public function clearAll()
    {
        $user = Auth::user();

        UserNotification::where('user_id', $user->id)->delete();

        return redirect()->back()->with('success', 'Semua notifikasi berhasil dibersihkan.');
    }

    /**
     * API: get unread count (for badge polling).
     */
    public function unreadCount()
    {
        $count = UserNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
