<?php

namespace Tests\Feature;

use App\Models\Field;
use App\Models\Module;
use App\Models\Search;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        $this->user = User::find(1);
    }

    public function test_can_get_records_from_all_modules()
    {
        Module::where('status', 1)
            ->get()
            ->each(function ($module) {
            $record = (array) Module::getRecord($module->id, 1);

                $response = $this->actingAs($this->user)
                    ->json('GET', route('search_data'),
                        [
                            'module_id' => $module->id,
                            'search_type' => 'module',
                            'per_page' => 1,
                        ]);

                $fields = Field::where('module_id', $module->id)->pluck('name')->toArray();
                foreach ($record as $key => $value) {
                    if (in_array($key, $fields)) {
                        $this->assertEquals($response['data'][0][$module->name.'__'.$key], $value);
                    }
                }
            });
    }

    public function test_module_search_results_are_matching_input_value()
    {
        Module::where('status', 1)
            ->where('admin', 0)
            ->get()
            ->each(function ($module) {
                $record = (array) Module::getRecord($module->id, 1);
                for ($x = 0; $x < (Field::where('module_id', $module->id)->count() + 1); $x++) {
                    $field = Field::where('module_id', $module->id)->inRandomOrder()->take(1)->value('name');
                    $response = $this->actingAs($this->user)
                        ->json('GET', route('search_data'),
                            [
                                'module_id' => $module->id,
                                'search_type' => 'module',
                                'per_page' => 1,
                                ' . $module->id . "__" . $field . ' => ' . $record[$field] . ',
                            ]);
                    $this->assertEquals($response['data'][0][$module->name.'__'.$field], $record[$field]);
                }
        });
    }

    public function test_can_search_modules_multiple_values()
    {
        Module::where('status', 1)
            ->where('admin', 0)
            ->get()
            ->each(function ($module) {
                $count = Field::where('module_id', $module->id)->count();
                if ($count > 0) {

                    $record = (array) Module::getRecord($module->id, rand(1, 2));
                    $fields = Field::where('module_id', $module->id)
                        ->whereNotIn('input_type', Search::$excludeFieldTypes['Search'])
                        ->inRandomOrder()
                        ->take(2)
                        ->pluck('name');

                    $data = [];
                    $data['per_page'] = 1;
                    $data['search_type'] = 'module';
                    $data['module_id'] = $module->id;
                    foreach ($fields as $field) {
                        $data[$module->id.'__'.$field] = $record[$field];
                    }

                    $response = $this->actingAs($this->user)
                        ->json('GET', route('search_data'), $data);
                    foreach ($fields as $field) {
                        $this->assertNotEmpty($response['data'][0][$module->name.'__'.$field]);
                        $this->assertEquals($response['data'][0][$module->name.'__'.$field], $record[$field]);
                    }
                }
            });
    }
}
