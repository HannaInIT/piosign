<?php

namespace App\Services;

use Google\Service\Directory;
use Google\Service\Directory\User;
use Illuminate\Support\Collection;

class GoogleDirectoryService
{
    public function __construct(private Directory $directoryService) {}

    public function listUsers(): Collection
    {
        $rawUsers = $this->fetchAllUsers();

        return $this->filterActiveUsers($rawUsers)
            ->map(fn (User $user) => $this->mapUser($user));
    }

    private function fetchAllUsers(): Collection
    {
        $allUsers = collect();
        $pageToken = null;

        do {
            $response = $this->fetchPage($pageToken);
            $allUsers = $allUsers->merge($response->getUsers() ?? []);
            $pageToken = $response->getNextPageToken();
        } while ($pageToken);

        return $allUsers;
    }

    private function fetchPage(?string $pageToken): Directory\Users
    {
        return $this->directoryService->users->listUsers([
            'domain' => config('services.google.domain'),
            'pageToken' => $pageToken,
        ]);
    }

    private function filterActiveUsers(Collection $users): Collection
    {
        return $users
            ->filter(fn (User $user) => ! $user->getSuspended() && ! $user->getArchived())
            ->values();
    }

    private function mapUser(User $user): array
    {
        return [
            'google_id' => $user->getId(),
            'email' => $user->getPrimaryEmail(),
            'first_name' => $user->getName()->getGivenName(),
            'last_name' => $user->getName()->getFamilyName(),
            'suspended' => $user->getSuspended(),
            'archived' => $user->getArchived(),
        ];
    }
}
