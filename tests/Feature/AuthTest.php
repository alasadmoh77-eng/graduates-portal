<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\User;
use App\Models\Major;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_graduate_can_register()
    {
        $major = Major::create(['name_ar' => 'Test', 'name_en' => 'Test']);
        
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'university_id' => '2023-001',
            'major_id' => $major->id,
            'graduation_year' => 2023,
        ]);

        $response->assertRedirect('/graduate/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }
}
