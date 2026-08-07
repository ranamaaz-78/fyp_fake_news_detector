<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class VerifyEmailNotification extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $logoUrl = rtrim(config('app.url'), '/').'/assets/images/'.rawurlencode('Color logo.svg');

        return (new MailMessage)
            ->subject('Verify your email — VeriFact AI')
            ->view('emails.verify-email', [
                'url' => $verificationUrl,
                'userName' => $notifiable->name ?? 'there',
                'logoUrl' => $logoUrl,
            ]);
    }

    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
