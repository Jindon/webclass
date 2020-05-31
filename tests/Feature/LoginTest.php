<?php

namespace Tests\Feature;

use App\Models\Superadmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function guest_can_view_superadmin_login_form()
    {
        $response = $this->get(route('superadmin.loginForm'));

        $response->assertStatus(200);

        $response->assertSee('Superadmin Login');
    }

    /** @test */
    public function superadmin_can_login_to_dashboard()
    {
        $superadmin = factory(Superadmin::class)->create();

        $this->post(route('superadmin.login'), [
            'email' => $superadmin->email,
            'password' => 'welcome'
        ])->assertRedirect(route('superadmin.home'));

        $this->assertAuthenticatedAs($superadmin, 'superadmin');
    }
}
