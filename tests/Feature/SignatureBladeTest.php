<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SignatureBladeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    #[Test]
    public function signature_renders_for_fully_rendered_employees()
    {
        $employee = Employee::factory()->create([
            'first_name' => 'Tina',
            'last_name' => 'James',
            'job_title' => 'Developer',
            'department' => 'Dev',
            'phone_number' => '+31616555487',
        ]);

        $html = view('components.signature', ['employee' => $employee])->render();

        $this->assertStringContainsString('Tina', $html);
        $this->assertStringContainsString('James', $html);
        $this->assertStringContainsString('Developer', $html);
        $this->assertStringContainsString('Dev', $html);
        $this->assertStringContainsString('+31616555487', $html);
        $this->assertStringContainsString('Met vriendelijke groet,', $html);
        $this->assertStringContainsString('0103400308', $html);

    }

    #[Test]
    public function signature_renders_without_errors_when_optional_fields_are_empty()
    {
        $employee = Employee::factory()->create([
            'first_name' => 'Tina',
            'last_name' => 'James',
            'job_title' => null,
            'department' => null,
            'phone_number' => null,
        ]);

        $html = view('components.signature', ['employee' => $employee])->render();

        $this->assertStringContainsString('Tina', $html);
        $this->assertStringContainsString('James', $html);
        $this->assertStringContainsString('Met vriendelijke groet,', $html);
        $this->assertStringContainsString('0103400308', $html);
    }

    #[Test]
    public function closing_line_is_always_present()
    {
        $employee = Employee::factory()->create();
        $html = view('components.signature', ['employee' => $employee])->render();
        $this->assertStringContainsString('Met vriendelijke groet,', $html);
        $this->assertStringContainsString('0103400308', $html);
    }

    #[Test]
    public function option_fields_are_absent_when_not_set()
    {
        $employee = Employee::factory()->create([
            'job_title' => null,
            'department' => null,
            'phone_number' => null,
        ]);

        $html = view('components.signature', ['employee' => $employee])->render();

        $this->assertStringNotContainsString('null', $html);
        $this->assertStringContainsString('0103400308', $html);
    }
}
