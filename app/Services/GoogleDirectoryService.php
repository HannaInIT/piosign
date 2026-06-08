<?php

namespace App\Services;

use Exception;
use Google\Client;
use Google\Service\Directory;
use Illuminate\Support\Collection;

class GoogleDirectoryService
{
    private Directory $directoryService;

    public function __construct(Client $client)
    {
        $client->setScopes([Directory::ADMIN_DIRECTORY_USER_READONLY]);

        $this->directoryService = new Directory($client);
    }

    public function listUsers(): Collection
    {
        try {
            $pageToken = null;
            $allUsersArray = [];

            do {
                $response = $this->directoryService->users->listUsers([
                    'domain' => config('services.google.domain'),
                    'pageToken' => $pageToken,
                ]);

                $users = $response->getUsers() ?? [];

                foreach ($users as $user) {
                    $allUsersArray[] = $user;
                }

                $pageToken = $response->getNextPageToken();
            } while ($pageToken);

            return collect($allUsersArray);
        } catch (Exception $exception) {
            report($exception);

            return collect();
        }
    }
}
