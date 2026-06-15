<?php

namespace Tests\Feature;

use App\Models\Relationship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubpanelTest extends TestCase
{
    use RefreshDatabase;

    public $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        $this->user = User::find(1);
    }

    public function test_can_delete_relationship_record()
    {

        $record = (array) Relationship::getRecord(1, 1);
        $this->assertNotNull($record['id']);

        $response = $this->actingAs($this->user)
            ->json('POST', 'data/delete/1/type/relationship',
                [
                    1,
                ]);
        $record = (array) Relationship::getRecord(1, 1);
        $this->assertEmpty($record);

    }

    public function test_can_add_a_subpanel_record_with_existing_records_selected()
    {
        $data = ['module_id_1' => '2',
            'module_id_2' => '17',
            'module_records' => [1 => '2', 2 => '17'],
            'save_type' => 'subpanel',
            'subpanel_id' => '1',
        ];
        $response = $this->actingAs($this->user)
            ->json('POST', '/data/subpanel/save', $data);

        $response->assertStatus(200);
    }

    public function test_can_add_a_subpanel_record_with_existing_record_and_new_module_record()
    {
        $data = [
            '2__assigned_to' => 1,
            '2__email' => 'sds@sdsd.com',
            '2__fax' => '+1 (513) 308-7862',
            '2__first_name' => 'Rocky',
            '2__last_name' => 'Streich',
            '2__phone' => '+1 (336) 430-6537',
            'module_id_1' => '2',
            'from_id' => 0,
            'from_module' => 0,
            'module_id_2' => '17',
            'module_records' => [1 => '2'],
            'save_type' => 'subpanel',
            'subpanel_id' => '1',
        ];
        $response = $this->actingAs($this->user)
            ->json('POST', '/data/subpanel/save', $data);

        $response->assertStatus(200);
    }

    public function test_can_edit_a_subpanel_record()
    {
        $data = [
            'record_id' => '5',
            'module_id_2' => '45',
            'module_records' => [1 => '1', 2 => 45],
            'save_type' => 'subpanel',
            'subpanel_id' => '1',
        ];
        $response = $this->actingAs($this->user)
            ->json('POST', '/data/subpanel/save', $data);

        $response->assertStatus(200);
    }
}
