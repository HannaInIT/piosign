<?php

namespace Tests\Feature;

use App\Jobs\SyncGoogleWorkspaceUsersJob;
use App\Models\Employee;
use App\Services\GoogleDirectoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SyncGoogleWorkspaceUsersJobTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_employees_from_google_workspace_users(): void
    {
        $this->mock(GoogleDirectoryService::class, function ($mock) {
            $mock->shouldReceive('listUsers')->once()->andReturn(collect([
                [
                    'google_id' => '111',
                    'email' => 'jan@pionect.nl',
                    'first_name' => 'Jan',
                    'last_name' => 'Jansen',
                    'suspended' => false,
                    'archived' => false,
                ],
            ]));
        });
        (new SyncGoogleWorkspaceUsersJob)->handle(app(GoogleDirectoryService::class));
        $this->assertDatabaseHas('employees', [
            'google_id' => '111',
            'email' => 'jan@pionect.nl',
            'first_name' => 'Jan',
        ]);
    }

    #[Test]
    public function it_restores_and_updates_employee(): void
    {
        $employee = Employee::factory()->create([
            'google_id' => '111',
            'first_name' => 'OldName',
        ]);
        $employee->delete();

        $this->mock(GoogleDirectoryService::class, function ($mock) {
            $mock->shouldReceive('listUsers')->once()->andReturn(collect([
                [
                    'google_id' => '111',
                    'email' => 'jan@pionect.nl',
                    'first_name' => 'Jan',
                    'last_name' => 'Jansen',
                    'suspended' => false,
                    'archived' => false,
                ],
            ]));
        });

        (new SyncGoogleWorkspaceUsersJob)->handle(app(GoogleDirectoryService::class));

        $this->assertDatabaseHas('employees', [
            'google_id' => '111',
            'first_name' => 'Jan',
            'deleted_at' => null,
        ]);
    }

    #[Test]
    public function it_soft_deletes_employees_missing_from_google_workspace(): void
    {
        Employee::factory()->create(['google_id' => '999']);

        $this->mock(GoogleDirectoryService::class, fn ($mock) => $mock->shouldReceive('listUsers')->once()->andReturn(collect([]))
        );

        (new SyncGoogleWorkspaceUsersJob)->handle(app(GoogleDirectoryService::class));

        $this->assertSoftDeleted('employees', ['google_id' => '999']);
    }
}
