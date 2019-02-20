<?php

namespace App\Api\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;;

class ResetPassword extends ResetPasswordNotification
{
    /**
     * The password reset token.
     *
     * @var string
     */
    public $id;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @param  string|int  $id
     */
    public function __construct($token, $id)
    {
        parent::__construct($token);
        $this->id = $id;
    }

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
            ->line('Вы получили это письмо т.к. запросили ссылку для сброса пароля')
            ->action('Сбросить пароль', url(config('app.front-url').'/password/reset/?user='.$this->id.'&token='.$this->token))
            ->line('Если Вы не запрашивали сброс пароля - просто проигнорируйте письмо');
    }
}
