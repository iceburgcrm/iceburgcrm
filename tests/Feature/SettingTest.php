<?php

namespace Tests\Feature;

use App\Models\Field;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Setting;

class SettingTest extends TestCase
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
    public function test_setting_page_found()
    {
        $response = $this->actingAs($this->user)->get('/settings');

        $response->assertStatus(200);
    }

    public function test_search_is_returning_default_amount()
    {
        $response = $this->actingAs($this->user)
            ->json('GET', route('search_data'),
                [
                    'module_id'=> 1,
                    'search_type' => 'module'
                ]);

        $response->assertJsonCount(Setting::getSetting('search_per_page'), 'data');

    }

}
