<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoAccountsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatcher_demo_account_is_seeded_with_operational_admin_role(): void
    {
        $this->seed(DatabaseSeeder::class);

        $dispatcher = User::query()
            ->where('email', 'dispatcher.demo@aka-logistics.test')
            ->firstOrFail();

        $this->assertSame('Dispatcher AKA Logistics', $dispatcher->name);
        $this->assertTrue(Hash::check('password', $dispatcher->password));
        $this->assertTrue($dispatcher->hasRole('admin_operasional'));
        $this->assertTrue($dispatcher->canAccessPanel(
            filament()->getPanel('admin')
        ));
    }
}
