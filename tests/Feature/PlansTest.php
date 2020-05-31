<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Superadmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PlansTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function superadmin_can_create_a_plan()
    {
        $superadmin = factory(Superadmin::class)->create();

        $attributes = factory(Plan::class)->raw();

        $this->actingAs($superadmin, 'superadmin')
            ->post(route('superadmin.plans.store'), $attributes)
            ->assertRedirect(route('superadmin.plans.index'));

        $this->assertDatabaseHas('plans', $attributes);

        $this->actingAs($superadmin, 'superadmin')
            ->get(route('superadmin.plans.index'))
            ->assertSee($attributes['name']);
    }

    /** @test */
    public function a_plan_requires_a_name()
    {
        $superadmin = factory('App\Models\Superadmin')->create();

        $attributes = factory(Plan::class)->raw(['name' => '']);

        $this->actingAs($superadmin, 'superadmin')
            ->post(route('superadmin.plans.store'), [])
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function a_plan_requires_a_status()
    {
        $superadmin = factory('App\Models\Superadmin')->create();

        $attributes = factory(Plan::class)->raw(['status' => '']);

        $this->actingAs($superadmin, 'superadmin')
            ->post(route('superadmin.plans.store'), [])
            ->assertSessionHasErrors('status');
    }

    /** @test */
    public function a_plan_requires_a_max_students()
    {
        $superadmin = factory('App\Models\Superadmin')->create();

        $attributes = factory(Plan::class)->raw(['max_students' => '']);

        $this->actingAs($superadmin, 'superadmin')
            ->post(route('superadmin.plans.store'), [])
            ->assertSessionHasErrors('max_students');
    }

    /** @test */
    public function a_plan_requires_a_max_uploads()
    {
        $superadmin = factory('App\Models\Superadmin')->create();

        $attributes = factory(Plan::class)->raw(['max_uploads' => '']);

        $this->actingAs($superadmin, 'superadmin')
            ->post(route('superadmin.plans.store'), [])
            ->assertSessionHasErrors('max_uploads');
    }

    /** @test */
    public function a_plan_requires_a_price()
    {
        $superadmin = factory('App\Models\Superadmin')->create();

        $attributes = factory(Plan::class)->raw(['price' => '']);

        $this->actingAs($superadmin, 'superadmin')
            ->post(route('superadmin.plans.store'), [])
            ->assertSessionHasErrors('price');
    }
}
