<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Confirme seu e-mail — Rota Segura')
            ->greeting('Olá, ' . ($notifiable->pessoa?->nome ?? 'usuário') . '!')
            ->line('Obrigado por se cadastrar no **Rota Segura**. Para ativar sua conta, confirme seu endereço de e-mail clicando no botão abaixo.')
            ->action('Confirmar E-mail', $verificationUrl)
            ->line('Este link expira em **60 minutos**.')
            ->line('Se você não criou uma conta no Rota Segura, ignore este e-mail — nenhuma ação é necessária.')
            ->salutation('Atenciosamente, Equipe Rota Segura');
    }

    protected function verificationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
