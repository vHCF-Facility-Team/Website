<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ConsoleRosterUpdateTest extends TestCase {
    #[Test]
    public function it_has_command(): void {
        $this->assertTrue(class_exists(\App\Console\Commands\RosterUpdate::class));
    }

    #[Test]
    public function it_runs_successfully(): void {
        $this->artisan('RosterUpdate:UpdateRoster')->assertSuccessful();
    }
}
