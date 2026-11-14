<?php

namespace Tests\Feature;

use App\Filament\Resources\RoleResource;
use App\Filament\Resources\RoleResource\Pages\CreateRole;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_management_is_only_available_to_super_admin(): void
    {
        $this->seed(RoleSeeder::class);

        $operationalAdmin = User::factory()->create();
        $operationalAdmin->assignRole('admin_operasional');

        $this->actingAs($operationalAdmin)
            ->get('/admin/roles')
            ->assertForbidden();

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin)
            ->get('/admin/roles')
            ->assertOk()
            ->assertSee('Manajemen Akses');
    }

    public function test_role_seeding_does_not_overwrite_managed_permissions(): void
    {
        $this->seed(RoleSeeder::class);

        $role = Role::findByName('admin_operasional', 'web');
        $role->revokePermissionTo('create shipments');

        $this->seed(RoleSeeder::class);

        $this->assertFalse(
            $role->fresh()->hasPermissionTo('create shipments')
        );
    }

    public function test_super_admin_can_create_a_role_and_assign_permissions(): void
    {
        $this->seed(RoleSeeder::class);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');
        $permission = Permission::findByName('view shipments', 'web');

        Livewire::actingAs($superAdmin)
            ->test(CreateRole::class)
            ->fillForm([
                'name' => 'dispatch_coordinator',
                'permissions' => [$permission->id],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $role = Role::findByName('dispatch_coordinator', 'web');

        $this->assertTrue($role->hasPermissionTo('view shipments'));
    }

    public function test_super_admin_role_cannot_be_edited_or_deleted(): void
    {
        $this->seed(RoleSeeder::class);
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');
        $this->actingAs($superAdmin);

        $superAdminRole = Role::findByName('super_admin', 'web');

        $this->assertFalse(RoleResource::canEdit($superAdminRole));
        $this->assertFalse(RoleResource::canDelete($superAdminRole));
    }
}
