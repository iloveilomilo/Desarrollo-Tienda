<?php

namespace App\Libraries;

class BrevoMailer
{
    public static function enviar(string $to, string $toName, string $subject, string $htmlContent): bool
    {
        $apiKey    = getenv('BREVO_API_KEY');
        $fromEmail = getenv('email.SMTPUser');

        if (empty($apiKey) || empty($fromEmail)) {
            log_message('error', 'BrevoMailer: falta BREVO_API_KEY o email.SMTPUser en el .env');
            return false;
        }

        $client = \Config\Services::curlrequest();

        try {
            $response = $client->post('https://api.brevo.com/v3/smtp/email', [
                'headers' => [
                    'accept'       => 'application/json',
                    'api-key'      => $apiKey,
                    'content-type' => 'application/json',
                ],
                'json' => [
                    'sender'      => ['name' => 'NewPhoneMX', 'email' => $fromEmail],
                    'to'          => [['email' => $to, 'name' => $toName]],
                    'subject'     => $subject,
                    'htmlContent' => $htmlContent,
                ],
                'http_errors' => false,
            ]);

            $status = $response->getStatusCode();

            if ($status >= 200 && $status < 300) {
                return true;
            }

            log_message('error', 'BrevoMailer: fallo al enviar correo. HTTP ' . $status . ' - ' . $response->getBody());
            return false;

        } catch (\Exception $e) {
            log_message('error', 'BrevoMailer: excepcion al enviar correo: ' . $e->getMessage());
            return false;
        }
    }
}
