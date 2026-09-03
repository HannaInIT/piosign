<?php

namespace App\Services;

use Closure;
use Google\Service\Gmail\SendAs;

class GoogleGmailService
{
    private Closure $gmailFactory;

    public function __construct()
    {
        $this->gmailFactory = app('google-gmail-factory');
    }

    public function updateUserSignature(string $userEmail, string $signatureHtml): void
    {
        $gmailService = ($this->gmailFactory)($userEmail);
        $sendAs = new SendAs;
        $sendAs->setSignature($signatureHtml);
        $gmailService->users_settings_sendAs->patch($userEmail, $userEmail, $sendAs);
    }
}
