<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Superadmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlansTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function superadmin_can_create_a_plan()
    {
        $this->be(factory(Superadmin::class)->create(), 'superadmin');

        $attributes = factory(Plan::class)->raw();

        $this->post(route('superadmin.plans.store'), $attributes)
            ->assertRedirect(route('superadmin.plans.index'));

        $this->assertDatabaseHas('plans', $attributes);

        $this->get(route('superadmin.plans.index'))
            ->assertSee($attributes['name']);
    }

    /** @test */
    public function superadmin_can_update_a_plan()
    {
        $this->be(factory(Superadmin::class)->create(), 'superadmin');
        $plan = factory(Plan::class)->create();

        $attributes = factory(Plan::class)->raw();

        $this->patch(route('superadmin.plans.update', $plan->id), $attributes)
            ->assertRedirect(route('superadmin.plans.index'))
            ->assertSessionHas(['message' => 'Plan updated successfully!']);

        $this->assertDatabaseHas('plans', array_merge($attributes, [
            'id' => $plan->id
        ]));
    }

    /** @test */
    public function superadmin_can_delete_a_plan()
    {
        $this->be(factory(Superadmin::class)->create(), 'superadmin');
        $plan = factory(Plan::class)->create();

        $this->delete(route('superadmin.plans.delete', $plan->id))
            ->assertRedirect(route('superadmin.plans.index'))
            ->assertSessionHas(['message' => 'Plan deleted successfully!']);

        $this->assertDeleted('plans', $plan->toArray());
    }

    /** @test */
    public function a_plan_requires_a_name()
    {
        $this->be(factory(Superadmin::class)->create(), 'superadmin');

        $attributes = factory(Plan::class)->raw(['name' => '']);

        $this->post(route('superadmin.plans.store'), $attributes)
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function a_plan_requires_a_status()
    {
        $this->be(factory(Superadmin::class)->create(), 'superadmin');

        $attributes = factory(Plan::class)->raw(['status' => '']);

        $this->post(route('superadmin.plans.store'), $attributes)
            ->assertSessionHasErrors('status');
    }

    /** @test */
    public function a_plan_requires_a_max_students()
    {
        $this->be(factory(Superadmin::class)->create(), 'superadmin');

        $attributes = factory(Plan::class)->raw(['max_students' => '']);

        $this->post(route('superadmin.plans.store'), $attributes)
            ->assertSessionHasErrors('max_students');
    }

    /** @test */
    public function a_plan_requires_a_max_uploads()
    {
        $this->be(factory(Superadmin::class)->create(), 'superadmin');

        $attributes = factory(Plan::class)->raw(['max_uploads' => '']);

        $this->post(route('superadmin.plans.store'), $attributes)
            ->assertSessionHasErrors('max_uploads');
    }

    /** @test */
    public function a_plan_requires_a_price()
    {
        $this->be(factory(Superadmin::class)->create(), 'superadmin');

        $attributes = factory(Plan::class)->raw(['price' => '']);

        $this->post(route('superadmin.plans.store'), $attributes)
            ->assertSessionHasErrors('price');
    }
}
