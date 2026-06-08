<?php

namespace App\Services;

use Exception;
use Google\Client;
use Google\Service\Gmail;

class GoogleGmailService
{
    private Client $client;

    public function __construct(Client $client)
    {
        $client->setScopes([Gmail::GMAIL_SETTINGS_BASIC]);
        $this->client = $client;
    }

    public function updateUserSignature(string $userEmail, string $signatureHtml): bool
    {
        try {
            $gmailClient = clone $this->client;
            $gmailClient->setSubject($userEmail);

            $service = new Gmail($gmailClient);
            $signature = new Gmail\SendAs;
            $signature->setSignature($signatureHtml);

            $service->users_settings_sendAs->patch(
                $userEmail,
                $userEmail,
                $signature
            );

            return true;
        } catch (Exception $exception) {
            report($exception);

            return false;
        }
    }
}
