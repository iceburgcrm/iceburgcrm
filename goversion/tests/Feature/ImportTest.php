<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportTest extends TestCase
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
    public function test_import_page()
    {
        $response = $this->actingAs($this->user)->get('/import');

        $response->assertStatus(200);
    }

    public function test_import_page_with_module()
    {
        $response = $this->actingAs($this->user)->get('/import?from_module_id=1');

        $response->assertStatus(200);
    }
}
