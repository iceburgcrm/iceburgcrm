<?php

namespace Tests\Feature;

use App\Models\Datalet;
use App\Models\Field;
use App\Models\Module;
use App\Models\ModuleSubpanel;
use App\Models\Relationship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuilderTest extends TestCase
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
    public function test_builder_page_displaying()
    {
        $response = $this->actingAs($this->user)->get('/admin/builder');

        $response->assertStatus(200);
    }

    public function test_select_account_is_returning_for_all_modules()
    {
        Module::where('status', 1)->get()->each(function ($module) {
            $response = $this->actingAs($this->user)->get('/data/builder/' . $module->id . '/type/select_module');
            $response->assertStatus(200);
            $this->assertGreaterThan(0, count($response->json()['module']));
            $this->assertGreaterThan(0, count($response->json()['fields']));
        });
    }

    public function test_create_a_module()
    {
        $name='test';
        $response = $this->actingAs($this->user)->post('/data/builder/0/type/add_module',
        ['name' => $name]);
        $response->assertStatus(200);
        $id=Module::where('name', 'like', $name)->value('id');
        $this->assertGreaterThan(0,$id);

        $response = $this->actingAs($this->user)->post('/data/builder/0/type/save',
            ['type' => 'module',
                'key' => 'name',
                'value' => 'new_name',
                'type_id' => $id]);
        $response->assertStatus(200);
        $this->assertEquals($id, Module::where('name', 'like', 'new_name')->value('id'));

        $response = $this->actingAs($this->user)->post('/data/builder/0/type/delete',
            ['type' => 'module',
                'delete_id' => $id]);
        $response->assertStatus(200);
        $this->assertEquals(0, Module::where('id', $id)->value('id'));

    }

    public function test_create_a_field()
    {
        $name='test';
        $response = $this->actingAs($this->user)->post('/data/builder/1/type/add_field',
            ['name' => $name]);
        $response->assertStatus(200);
        $id=Field::where('name', 'like', $name)->value('id');
        $this->assertGreaterThan(0,$id);

        $response = $this->actingAs($this->user)->post('/data/builder/1/type/save',
            ['type' => 'field',
                'key' => 'name',
                'value' => 'new_name',
                'type_id' => $id]);
        $response->assertStatus(200);
        $this->assertEquals($id, Field::where('name', 'like', 'new_name')->value('id'));

        $response = $this->actingAs($this->user)->post('/data/builder/1/type/delete',
            ['type' => 'field',
                'delete_id' => $id]);
        $response->assertStatus(200);
        $this->assertEquals(0, Field::where('id', $id)->value('id'));

    }

    public function test_create_a_subpanel()
    {
        $name='test';
        $response = $this->actingAs($this->user)->post('/data/builder/1/type/add_subpanel',
            ['name' => $name]);
        $response->assertStatus(200);
        $id=ModuleSubpanel::where('name', 'like', $name)->value('id');
        $this->assertGreaterThan(0,$id);

        $response = $this->actingAs($this->user)->post('/data/builder/0/type/save',
            ['type' => 'subpanel',
                'key' => 'name',
                'value' => 'new_name',
                'type_id' => $id]);
        $response->assertStatus(200);
        $this->assertEquals($id, ModuleSubpanel::where('name', 'like', 'new_name')->value('id'));

        $response = $this->actingAs($this->user)->post('/data/builder/0/type/delete',
            ['type' => 'subpanel',
                'delete_id' => $id]);
        $response->assertStatus(200);
        $this->assertEquals(0, ModuleSubpanel::where('id', $id)->value('id'));
    }

    public function test_create_a_relationship()
    {
        $name='test';
        $response = $this->actingAs($this->user)->post('/data/builder/0/type/add_relationship',
            ['name' => $name, 'relationship_modules' => '1,2']);
        $response->assertStatus(200);
        $id=Relationship::where('name', 'like', $name)
            ->value('id');
        $this->assertGreaterThan(0,$id);

        $response = $this->actingAs($this->user)->post('/data/builder/0/type/save',
            ['type' => 'relationship',
                'key' => 'name',
                'value' => 'new_name',
                'type_id' => $id]);
        $response->assertStatus(200);
        $this->assertEquals($id, Relationship::where('name', 'like', 'new_name')->value('id'));

        $response = $this->actingAs($this->user)->post('/data/builder/0/type/delete',
            ['type' => 'relationship',
                'delete_id' => $id]);
        $response->assertStatus(200);
        $this->assertEquals(0, Relationship::where('id', $id)->value('id'));
    }

    public function test_create_update_delete_a_datalet()
    {
        $name='test';
        $response = $this->actingAs($this->user)->post('/data/builder/0/type/add_datalet',
            ['name' => $name]);
        $response->assertStatus(200);
        $id=Datalet::where('label', 'like', strtolower($name))->value('id');
        $this->assertGreaterThan(0,$id);

        $response = $this->actingAs($this->user)->post('/data/builder/0/type/save',
            ['type' => 'datalet',
                'key' => 'name',
                'value' => 'new_name',
                'type_id' => $id]);
        $response->assertStatus(200);
        $this->assertEquals($id, Datalet::where('name', 'like', 'new_name')->value('id'));

        $response = $this->actingAs($this->user)->post('/data/builder/0/type/delete',
            ['type' => 'datalet',
                'delete_id' => $id]);
        $response->assertStatus(200);
        $this->assertEquals(0, Datalet::where('id', $id)->value('id'));
    }
}
