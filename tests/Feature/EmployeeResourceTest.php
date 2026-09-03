<?php

namespace Tests\Feature;

use App\Filament\Resources\Employees\Pages\EditEmployee;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmployeeResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->admin = User::factory()->create();
    }

    #[Test]
    public function admin_can_view_employees_list()
    {
        Employee::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get('/admin/employees')
            ->assertOk();
    }

    #[Test]
    public function edit_page_loads_for_existing_employee()
    {
        $employee = Employee::factory()->create();

        $this->actingAs($this->admin)
            ->get('/admin/employees/'.$employee->getKey().'/edit')
            ->assertOk();
    }

    #[Test]
    public function admin_can_update_editable_fields()
    {
        $employee = Employee::factory()->create([
            'job_title' => 'Developer',
            'department' => 'Dev',
            'phone_number' => '+31641212344',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(EditEmployee::class, [
            'record' => $employee->getKey(),
        ])
            ->fillForm([
                'job_title' => 'Senior developer',
                'department' => 'Dev',
                'phone_number' => '+31641919999',

            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('employees', [
            'uuid' => $employee->uuid,
            'job_title' => 'Senior developer',
            'department' => 'Dev',
            'phone_number' => '+31641919999',
        ]);
    }

    #[Test]
    public function google_workspace_fields_are_read_only_and_cannot_be_updated()
    {
        $employee = Employee::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Brown',
            'email' => 'some@company.com',
            'job_title' => 'Developer',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(EditEmployee::class, [
            'record' => $employee->getKey(),
        ])
            ->fillForm([
                'job_title' => 'Lead developer',
                'first_name' => 'First name',
                'last_name' => 'Last name',
                'email' => 'first@company.com',
            ])
            ->call('save');

        $this->assertDatabaseHas('employees', [
            'uuid' => $employee->uuid,
            'first_name' => 'John',
            'last_name' => 'Brown',
            'email' => 'some@company.com',
            'job_title' => 'Lead developer',
        ]);
    }

    #[Test]
    public function employee_with_no_optional_fields_loads_without_errors()
    {
        $employee = Employee::factory()->create([
            'job_title' => null,
            'department' => null,
            'phone_number' => null,
        ]);

        $this->actingAs($this->admin)
            ->get('/admin/employees/'.$employee->getKey().'/edit')
            ->assertOk();
    }
}
