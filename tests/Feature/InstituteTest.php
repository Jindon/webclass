<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Superadmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstituteTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function superadmin_can_create_a_plan()
    {
        $superadmin = factory(Superadmin::class)->create();

        $plan = factory(Plan::class)->create();

        $attributes = factory(Institute::class)->raw();

        $this->actingAs($superadmin, 'superadmin')
            ->post(route('superadmin.institutes.store'), $attributes)
            ->assertRedirect(route('superadmin.institutes.index'));

        $this->assertDatabaseHas('institutes', $attributes);

        $this->actingAs($superadmin, 'superadmin')
            ->get(route('superadmin.institutes.index'))
            ->assertSee($attributes['name']);
    }
}
