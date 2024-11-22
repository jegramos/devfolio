<?php

namespace App\Notifications;

use App\Enums\Queue;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class QueuedVerifyEmailNotification extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    private string $userGivenName;
    private int $expirationTimeMinutes;

    public function __construct(mixed $notifiable, int $expirationTimeMinutes)
    {
        $this->onQueue(Queue::EMAILS->value);

        /** @var User $notifiable */
        $this->userGivenName = $notifiable->userProfile->given_name;
        $this->expirationTimeMinutes = $expirationTimeMinutes;
    }

    protected function buildMailMessage($url): MailMessage
    {
        $appName = config('app.name');

        return (new MailMessage())
            ->subject('Verify Your Email Address')
            ->greeting('Hey, ' . $this->userGivenName . '!')
            ->line("Thank you for registering to $appName.")
            ->line(
                "Please click the button below to verify your email address. 
               Please note that this link will expire in $this->expirationTimeMinutes minutes."
            )
            ->action('Verify Email Address', $url)
            ->line('If you did not create an account, please ignore this email.');
    }
}
