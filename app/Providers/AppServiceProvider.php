<?php

namespace App\Providers;

use Google\Client;
use Google\Service\Directory;
use Google\Service\Gmail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Directory::class, function () {
            $client = $this->makeBaseClient();
            $client->setScopes([Directory::ADMIN_DIRECTORY_USER_READONLY]);
            $client->setSubject(config('services.google.admin_email'));

            return new Directory($client);
        });

        $this->app->bind('google-gmail-factory', function () {
            return function (string $userEmail) {
                $client = $this->makeBaseClient();

                $client->setScopes([Gmail::GMAIL_SETTINGS_BASIC]);
                $client->setSubject($userEmail);

                return new Gmail($client);
            };
        });
    }

    private function makeBaseClient(): Client
    {
        $client = new Client;
        $client->setAuthConfig(base_path(config('services.google.credentials')));

        return $client;
    }
}
