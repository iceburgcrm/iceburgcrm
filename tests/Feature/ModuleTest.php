<?php

namespace Tests\Feature;

use App\Models\Field;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        $this->user = User::find(1);
    }

    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_module_search_pages_are_reachable()
    {
        Module::where('status', 1)->get()->each(function ($module) {
            $response = $this->actingAs($this->user)->get('/module/'.$module->name);
            $response->assertStatus(200);
        });
    }

    public function test_module_detail_pages_are_reachable()
    {
        Module::where('status', 1)->get()->each(function ($module) {
            $response = $this->actingAs($this->user)->get('/module/'.$module->name.'/view/1');
            $response->assertStatus(200);
        });
    }

    public function test_module_add_pages_are_reachable()
    {
        Module::where('status', 1)->get()->each(function ($module) {
            $response = $this->actingAs($this->user)->get('/module/'.$module->name.'/add');
            $response->assertStatus(200);
        });
    }

    public function test_module_edit_pages_are_reachable()
    {
        Module::where('status', 1)->get()->each(function ($module) {
            $response = $this->actingAs($this->user)->get('/module/'.$module->name.'/edit/1');
            $response->assertStatus(200);
        });
    }

    public function test_module_has_fields()
    {
        Module::where('status', 1)->with('fields')->get()->each(function ($module) {
            $this->assertGreaterThan(0, count($module->fields));
        });
    }

    public function test_all_modules_have_records()
    {
        Module::where('status', 1)->with('fields')->get()->each(function ($module) {
            $this->assertNotEmpty(Module::getRecord($module->id, 1));
        });
    }

    public function test_all_modules_page_is_reachable()
    {
        $response = $this->actingAs($this->user)->get('/modules');

        $response->assertStatus(200);
    }

    public function test_can_export_modules()
    {
        Module::where('status', 1)->with('fields')->get()->each(function ($module) {
            $response = $this->actingAs($this->user)->post('/data/download/'.$module->id.'/csv', []);
            $response->assertStatus(200)
                ->assertDownload();
        });
    }

    public function test_can_delete_a_module_records()
    {
        $module = Module::find(1);
        $record = (array) Module::getRecord($module->id, 1);
        $this->assertNotNull($record);

        $response = $this->actingAs($this->user)
            ->json('POST', 'data/delete/1/type/module',
                [
                    1,
                ]);
        $record = (array) Module::getRecord($module->id, 1);
        $this->assertEmpty($record);

    }

    public function test_can_delete_many_module_records()
    {
        $module = Module::find(1);
        $record = (array) Module::getRecord($module->id, 1);
        $this->assertNotNull($record);

        $response = $this->actingAs($this->user)
            ->json('POST', 'data/delete/1/type/module',
                [
                    1, 2,
                ]);
        $record = (array) Module::getRecord($module->id, 1);
        $this->assertEmpty($record);
        $record = (array) Module::getRecord($module->id, 2);
        $this->assertEmpty($record);
    }

    public function test_add_page_reachable()
    {

        $module = Module::where('status', 1)->first();
        $response = $this->actingAs($this->user)->get('/module/'.$module->name.'/add');
        $response->assertStatus(200);

    }

    public function test_can_add_a_module_record_with_all_fields()
    {
        Module::where('status', 1)
            ->where('name', 'not like', 'ice_users')
            ->where('admin', 0)
            ->get()
            ->each(function ($module) {
                for ($x = 0; $x < (Field::where('module_id', $module->id)->count() + 1); $x++) {

                    $fields = Field::where('module_id', $module->id)
                        ->where('name', 'NOT LIKE', 'ice_slug')->get();
                    $data = [];
                    $data['module_id'] = $module->id;
                    $data['search_type'] = 'module';
                    foreach ($fields as $field) {
                        $data[$module->id.'__'.$field->name] = rand(0, 99999);
                    }
                    $response = $this->actingAs($this->user)
                        ->json('POST', '/data/save', $data);

                    $response->assertStatus(200);
                }
            });
    }
}
