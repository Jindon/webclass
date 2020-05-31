<?php

namespace Tests\Feature;

use App\Models\Institute;
use App\Models\InstitutePlan;
use App\Models\Plan;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InstitutesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function superadmin_can_create_an_institute()
    {
        Storage::fake('public');

        $this->signIn('superadmin');

        $plan = factory(Plan::class)->create();

        $attributes = factory(Institute::class)->raw();

        $admin_attributes = factory(Admin::class)->raw([
            'password' => 'welcome',
            'password_confirmation' => 'welcome'
        ]);

        $attributes['logo'] = UploadedFile::fake()->image('logo.jpg')->size(1000);
        $attributes['plan_id'] = $plan->id;
        $attributes['admin'] = $admin_attributes;

        $this->post(route('superadmin.institutes.store'), $attributes)
            ->assertRedirect(route('superadmin.institutes.index'))
            ->assertSessionHas(['message' => 'Institute created successfully!']);

        $this->assertDatabaseHas('institutes', [
            'name' => $attributes['name']
        ]);

        $institute = Institute::first();
        $this->assertDatabaseHas('institute_plans', [
            'plan_id' => $plan->id,
            'institute_id' => $institute->id,
            'start_date' => Carbon::now()->toDate(),
            'end_date' => Carbon::now()->addYear()->toDate(),
        ]);

        $this->assertDatabaseHas('admins', [
            'institute_id' => $institute->id,
            'name' => $admin_attributes['name'],
            'email' => $admin_attributes['email'],
            'password' => $admin_attributes['password']
        ]);

        Storage::disk('public')->assertExists("logos/{$institute->logo}");

        $this->get(route('superadmin.institutes.index'))
            ->assertSee($attributes['name']);
    }
    /** @test */
    public function superadmin_can_update_an_institute() {
        $this->signIn('superadmin');

        $basic_plan = factory(Plan::class)->create();
        $premium_plan = factory(Plan::class)->create();
        $institute = factory(Institute::class)->create();
        factory(InstitutePlan::class)->create([
            'institute_id' => $institute->id,
            'plan_id' => $basic_plan->id
        ]);
        factory(Admin::class)->create(['institute_id' => $institute->id]);
        $attributes = factory(Institute::class)->raw();
        $admin_attributes = factory(Admin::class)->raw([
            'institute_id' => $institute->id,
            'password' => 'welcome',
            'password_confirmation' => 'welcome'
        ]);

        $attributes['plan_id'] = $premium_plan->id;
        $attributes['admin'] = $admin_attributes;

        $this->patch(route('superadmin.institutes.update', $institute->id), $attributes)
            ->assertRedirect(route('superadmin.institutes.index'))
            ->assertSessionHas(['message' => 'Institute updated successfully!']);

        $this->assertDatabaseHas('institutes', [
            'id' => $institute->id,
            'name' => $attributes['name'],
            'board' => $attributes['board'],
        ]);

        $this->assertDatabaseHas('admins', [
            'institute_id' => $institute->id,
            'name' => $admin_attributes['name'],
            'email' => $admin_attributes['email'],
        ]);

        $this->assertDatabaseHas('institute_plans', [
            'institute_id' => $institute->id,
            'plan_id' => $premium_plan->id,
            'status' => true
        ]);

        $this->assertTrue(Hash::check('welcome', $institute->admin->password), 'Password is not updated correctly!');
    }

    /** @test */
    public function superadmin_can_manage_an_institute_subscription() {
        $this->signIn('superadmin');
        $plan = factory(Plan::class)->create();
        $institute = factory(Institute::class)->create();
        $institute_plan = factory(InstitutePlan::class)->create([
            'institute_id' => $institute->id,
            'plan_id' => $plan->id
        ]);

        $attributes = [
            'start_date' => '2020-05-31',
            'end_date' => '2023-05-31'
        ];

        $this->patch(route('superadmin.institutes.manageSubscription', $institute->id), $attributes)
            ->assertRedirect(route('superadmin.institutes.index'))
            ->assertSessionHas(['message' => "Institute's subscription updated successfully!"]);

        $this->assertDatabaseHas('institute_plans', [
            'institute_id' => $institute->id,
            'plan_id' => $plan->id,
            'start_date' => '2020-05-31',
            'end_date' => '2023-05-31'
        ]);
    }

    /** @test */
    public function superadmin_can_delete_an_institute() {
        $this->signIn('superadmin');

        $plan = factory(Plan::class)->create();
        $institute = factory(Institute::class)->create();
        $institute_plan = factory(InstitutePlan::class)->create([
            'institute_id' => $institute->id,
            'plan_id' => $plan->id
        ]);

        $this->delete(route('superadmin.institutes.delete', $institute->id))
            ->assertRedirect(route('superadmin.institutes.index'))
            ->assertSessionHas(['message' => "Institute deleted successfully!"]);

        $this->assertDeleted('institutes', $institute->toArray());
        $this->assertDeleted('institute_plans', $institute_plan->toArray());
    }

    /** @test */
    public function an_institute_requires_a_name() {
        $this->signIn('superadmin');

        $attributes = factory(Institute::class)->raw(['name' => '']);
        $attributes['admin'] = factory(Admin::class)->raw();

        $this->post(route('superadmin.institutes.store'), $attributes)
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function an_institute_requires_a_board() {
        $this->signIn('superadmin');

        $attributes = factory(Institute::class)->raw(['board' => '']);
        $attributes['admin'] = factory(Admin::class)->raw();

        $this->post(route('superadmin.institutes.store'), $attributes)
            ->assertSessionHasErrors('board');
    }

    /** @test */
    public function an_institute_requires_an_admin_name() {
        $this->signIn('superadmin');

        $attributes = factory(Institute::class)->raw();
        $attributes['admin'] = factory(Admin::class)->raw(['name' => '']);

        $this->post(route('superadmin.institutes.store'), $attributes)
            ->assertSessionHasErrors('admin.name');
    }

    /** @test */
    public function an_institute_requires_an_admin_email() {
        $this->signIn('superadmin');

        $attributes = factory(Institute::class)->raw();
        $attributes['admin'] = factory(Admin::class)->raw(['email' => '']);

        $this->post(route('superadmin.institutes.store'), $attributes)
            ->assertSessionHasErrors('admin.email');
    }

    /** @test */
    public function an_institute_requires_an_admin_password() {
        $this->signIn('superadmin');

        $attributes = factory(Institute::class)->raw();
        $attributes['admin'] = factory(Admin::class)->raw(['password' => '']);

        $this->post(route('superadmin.institutes.store'), $attributes)
            ->assertSessionHasErrors('admin.password');
    }
}
