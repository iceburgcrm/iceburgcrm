<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SecurityRegressionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');

        $this->admin = User::findOrFail(1);
        $this->regularUser = User::findOrFail(2);
    }

    public function test_regular_user_cannot_save_connector_credentials(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->post('/data/connector/set_connector', [
                'name' => 'Blocked',
                'auth_type' => 'token',
                'access_token' => 'secret-token',
            ]);

        $response->assertRedirect('dashboard');
    }

    public function test_regular_user_cannot_create_connector_commands(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->post('/data/connector/add_command', [
                'connector_id' => 1,
                'name' => 'Blocked',
                'method_name' => 'execute',
                'description' => 'Blocked command',
                'class_name' => 'IceburgCRM',
            ]);

        $response->assertRedirect('dashboard');
    }

    public function test_import_requires_module_id_before_parsing_upload(): void
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/data/import', [
                'input_file' => UploadedFile::fake()->createWithContent('import.csv', "name\nAcme\n"),
                'preview' => true,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('module_id');
    }

    public function test_import_rejects_unapproved_file_types(): void
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/data/import', [
                'input_file' => UploadedFile::fake()->createWithContent('import.php', '<?php echo "bad";'),
                'module_id' => 1,
                'preview' => true,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('input_file');
    }
}
