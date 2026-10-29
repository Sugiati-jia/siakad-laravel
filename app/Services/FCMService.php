<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FCMService
{
    protected $messaging;

    public function __construct()
    {
        $credentialsPath = storage_path('app/firebase_credentials.json');
        
        if (file_exists($credentialsPath)) {
            $factory = (new Factory)->withServiceAccount($credentialsPath);
            $this->messaging = $factory->createMessaging();
        }
    }

    public function sendMulticast(array $tokens, $title, $body, $icon = null, $url = null)
    {
        if (!$this->messaging || empty($tokens)) {
            return false;
        }

        // We use data payload as well to support background sw logic consistently
        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body, $icon))
            ->withData([
                'url' => $url ?? url('/orangtua/notifications'),
                'title' => $title,
                'body' => $body,
                'icon' => $icon ?? url('/images/logo-sekolah.png')
            ]);

        try {
            $report = $this->messaging->sendMulticast($message, $tokens);
            return $report;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('FCM Multicast Error: ' . $e->getMessage());
            return false;
        }
    }
}
