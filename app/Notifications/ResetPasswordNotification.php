<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Redefinição de senha — Rota Segura')
            ->greeting('Olá, ' . ($notifiable->pessoa?->nome ?? 'usuário') . '!')
            ->line('Recebemos uma solicitação para redefinir a senha da sua conta no **Rota Segura**.')
            ->action('Redefinir Senha', $url)
            ->line('Este link expira em **60 minutos**.')
            ->line('Se você não solicitou a redefinição de senha, ignore este e-mail. Sua senha permanece a mesma.')
            ->salutation('Atenciosamente, Equipe Rota Segura');
    }
}
