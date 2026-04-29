<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_authenticated_users_can_still_open_role_registration_screens(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/register?role=user')
            ->assertOk()
            ->assertSee('User Registration');

        $this->actingAs($user)
            ->get('/register?role=farmer')
            ->assertOk()
            ->assertSee('Farmer Registration');
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('marketplace', absolute: false));
    }

    public function test_new_buyer_users_are_redirected_to_marketplace(): void
    {
        $response = $this->post('/register', [
            'account_type' => 'user',
            'name' => 'Buyer User',
            'email' => 'buyer@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('marketplace', absolute: false));
    }

    public function test_new_farmer_users_are_redirected_to_dashboard(): void
    {
        $response = $this->post('/register', [
            'account_type' => 'farmer',
            'name' => 'Farmer User',
            'email' => 'farmer@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
