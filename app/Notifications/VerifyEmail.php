<?php

namespace App\Notifications;


use Illuminate\Auth\Notifications\VerifyEmail as Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Notification
{
    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        $appUrl = config('app.client_url', config('app.url'));
        $url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'hash' => sha1($notifiable->getEmailForVerification()),
                'user' => $notifiable->getKey(),
            ]
        );

        return str_replace(url('/api'), $appUrl, $url);
    }
}
