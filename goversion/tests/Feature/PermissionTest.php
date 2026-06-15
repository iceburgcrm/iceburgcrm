<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    public $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        $this->user = User::find(1);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_find_page()
    {
        $response = $this->actingAs($this->user)->get('/admin/permissions');

        $response->assertStatus(200);
    }

    public function test_can_set_can_read()
    {
        $original = [
            'can_read' => Permission::where('id', 1)->value('can_read'),
        ];

        $data = [
            'current_state' => $original['can_read'],
            'id' => 1,
            'type' => 'read',
        ];

        $response = $this->actingAs($this->user)
            ->json('POST', '/data/permissions/save', $data);

        $this->assertNotEquals($original['can_read'], Permission::where('id', 1)->value('can_read'));

    }

    public function test_can_set_can_write()
    {
        $original = [
            'can_write' => Permission::where('id', 1)->value('can_write'),
        ];

        $data = [
            'current_state' => $original['can_write'],
            'id' => 1,
            'type' => 'write',
        ];

        $response = $this->actingAs($this->user)
            ->json('POST', '/data/permissions/save', $data);

        $this->assertNotEquals($original['can_write'], Permission::where('id', 1)->value('can_write'));

    }

    public function test_can_set_can_export()
    {
        $original = [
            'can_export' => Permission::where('id', 1)->value('can_export'),
        ];

        $data = [
            'current_state' => $original['can_export'],
            'id' => 1,
            'type' => 'export',
        ];

        $response = $this->actingAs($this->user)
            ->json('POST', '/data/permissions/save', $data);

        $this->assertNotEquals($original['can_export'], Permission::where('id', 1)->value('can_export'));

    }

    public function test_can_set_can_import()
    {
        $original = [
            'can_import' => Permission::where('id', 1)->value('can_import'),
        ];

        $data = [
            'current_state' => $original['can_import'],
            'id' => 1,
            'type' => 'import',
        ];

        $response = $this->actingAs($this->user)
            ->json('POST', '/data/permissions/save', $data);

        $this->assertNotEquals($original['can_import'], Permission::where('id', 1)->value('can_import'));

    }

    public function test_admin_user_can_access_admin_area()
    {
        $response = $this->actingAs($this->user)->get('/admin/permissions');
        $response->assertStatus(200);

    }

    public function test_regular_user_cannot_access_admin_area()
    {
        $user = User::find(2);
        $response = $this->actingAs($user)->get('/admin/permissions');
        $response->assertStatus(302);

    }

    public function test_cannot_read_module_without_permission()
    {
        $data = [
            'current_state' => 1,
            'id' => 1,
            'type' => 'read',
        ];

        $this->actingAs($this->user)
            ->json('POST', '/data/permissions/save', $data);

        $module = Module::find(1);
        $this->actingAs($this->user)->get('/module/'.$module->name)
                    ->assertStatus(302);
    }

    public function test_cannot_write_module_without_permission()
    {
        $data = [
            'current_state' => 1,
            'id' => 1,
            'type' => 'write',
        ];

        $this->actingAs($this->user)
            ->json('POST', '/data/permissions/save', $data);

        $response = $this->actingAs($this->user)->get('/admin/permissions');

        $module = Module::find(1);
        $this->actingAs($this->user)->get('/module/'.$module->name.'/edit/1')
            ->assertStatus(302);
    }
}
