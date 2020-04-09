<?php

namespace Umomega\Auth\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as Notification;
use Umomega\Foundation\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class ResetPassword extends Notification
{

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return (new MailMessage)
            ->subject(Lang::get('auth::passwords.mail_subject'))
            ->line(Lang::get('auth::passwords.mail_reason'))
            ->action(Lang::get('auth::passwords.reset_password'), url(config('app.url').route('reactor.password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()], false)))
            ->line(Lang::get('auth::passwords.mail_duration', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(Lang::get('auth::passwords.mail_no_action'));
    }

}
