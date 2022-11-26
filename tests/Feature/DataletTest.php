<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Datalet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DataletTest extends TestCase
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
    public function test_all_active_datalets_are_returning_data()
    {
        $datalets=DataLet::where('active', 1)->get()->each(function ($item){
            $response = $this->actingAs($this->user)->get('/data/datalet?id=' . $item->id);
            $response->assertStatus(200);
        });

    }
}
