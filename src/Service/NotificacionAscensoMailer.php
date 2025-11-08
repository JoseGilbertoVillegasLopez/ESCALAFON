<?php

namespace App\Service;

use App\Entity\InformacionPersonal;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Mime\Address;

/**
 * Servicio para enviar notificaciones de ascenso a los trabajadores.
 */
final class NotificacionAscensoMailer
{
    public function __construct(
        private MailerInterface $mailer,
        private MessageBusInterface $bus,
        private string $fromEmail,
        private string $fromName,
        private string $soporteEmail,
        private string $appName,
        private string $loginUrl,
    ) {}

    /**
     * Envía una notificación informando al trabajador que tiene
     * una oportunidad de ascenso disponible en su categoría.
     */
    public function sendNotificacion(InformacionPersonal $trabajador, string $puestoDestino): void
    {
        $nombreCompleto = (string) $trabajador;
        $correo = $trabajador->getCorreo() ?? null;

        if (!$correo) {
            return; // si no tiene correo, no intentamos enviar
        }

        $email = (new TemplatedEmail())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to(new Address($correo))
            ->subject(sprintf('[%s] Notificación de oportunidad de ascenso', $this->appName))
            ->htmlTemplate('emails/notificacion_ascenso.html.twig')
            ->textTemplate('emails/notificacion_ascenso.txt.twig')
            ->context([
                'appName'        => $this->appName,
                'fecha'          => new \DateTimeImmutable(),
                'nombreCompleto' => $nombreCompleto,
                'puestoDestino'  => $puestoDestino,
                'loginUrl'       => $this->loginUrl,
                'soporteEmail'   => $this->soporteEmail,
            ]);

        $this->bus->dispatch(new SendEmailMessage($email));
    }
}
