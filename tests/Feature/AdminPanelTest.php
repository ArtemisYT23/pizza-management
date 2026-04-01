<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_when_visiting_admin_panel(): void
    {
        $this->get(route('admin'))
            ->assertRedirect(route('login'));
    }

    public function test_non_admin_users_are_redirected_to_home(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin'))
            ->assertRedirect(route('home'));
    }

    public function test_admin_users_can_visit_admin_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin'))
            ->assertOk();
    }

    public function test_admin_visiting_home_is_redirected_to_admin_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('home'))
            ->assertRedirect(route('admin'));
    }

    public function test_admin_visiting_my_orders_is_redirected_to_admin_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('orders.mine'))
            ->assertRedirect(route('admin'));
    }

    public function test_guest_can_visit_home_carta(): void
    {
        $this->get(route('home'))->assertOk();
    }

    public function test_legacy_menu_path_redirects_to_home(): void
    {
        $this->get('/menu')->assertRedirect('/');
    }
}
