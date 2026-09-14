<?php

namespace App\Notifications;

use App\Models\OtpChallenge;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpCodeNotification extends Notification
{
    public function __construct(
        private readonly string $code,
        private readonly string $purpose,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $prefix = trim((string) config('mail.email_subject_prefix', ''));
        $subject = $this->purpose === OtpChallenge::PURPOSE_LOGIN
            ? 'sign-in code'
            : 'verification code';

        return (new MailMessage)
            ->subject(trim($prefix.' Ochoka Heritage '.$subject))
            ->greeting('Ochoka Heritage')
            ->line("Your one-time {$subject} is:")
            ->line($this->code)
            ->line('This code expires in '.config('auth_otp.ttl_minutes', 10).' minutes.');
    }
}
