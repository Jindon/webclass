<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Institute;
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


    /** @test */
    public function guest_can_view_admin_login_form()
    {
        factory(Institute::class)->create(['subdomain' => 'institute']);
        $response = $this->get(route('admin.loginForm', ['subdomain' => 'institute']));

        $response->assertStatus(200);

        $response->assertSee('Admin Login');
    }

    /** @test */
    public function non_existing_subdomain_shows_404_error()
    {
        factory(Institute::class)->create(['subdomain' => 'institute']);
        $response = $this->get(route('admin.loginForm', ['subdomain' => 'another_institute']));

        $response->assertStatus(404);
    }

    /** @test */
    public function admin_can_login_to_dashboard()
    {
        $institute = factory(Institute::class)->create(['subdomain' => 'institute']);
        $admin = factory(Admin::class)->create([
            'institute_id' => $institute->id
        ]);

        $this->post(route('admin.login', ['subdomain' => 'institute']), [
            'email' => $admin->email,
            'password' => 'welcome'
        ])->assertRedirect(route('admin.home'));

        $this->assertAuthenticatedAs($admin, 'admin');
    }

    /** @test */
    public function admin_of_another_institute_cannot_login_to_another_institute_dashboard()
    {
        $institute1 = factory(Institute::class)->create(['subdomain' => 'institute']);
        $admin1 = factory(Admin::class)->create([
            'institute_id' => $institute1->id
        ]);

        $institute2 = factory(Institute::class)->create(['subdomain' => 'institute2']);
        $admin2 = factory(Admin::class)->create([
            'institute_id' => $institute2->id
        ]);

        $this->post(route('admin.login', ['subdomain' => 'institute']), [
            'email' => $admin2->email,
            'password' => 'welcome'
        ])->assertSessionHasErrors(['email' => 'The given email is not the admin email!']);
    }
}
