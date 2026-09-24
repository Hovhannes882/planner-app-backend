<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $token)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = config('app.frontend_url')
            . '/reset-password?token='
            . urlencode($this->token)
            . '&email='
            . urlencode($notifiable->email);


        return (new MailMessage)
            ->line('Reset your password')
            ->line('You requested a password reset.')
            ->action('Reset Password', $url)
            ->line('This password reset link will expire soon.');
    }
}
