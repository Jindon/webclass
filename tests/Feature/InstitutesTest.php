<?php

namespace Tests\Feature;

use App\Models\Institute;
use App\Models\Plan;
use App\Models\Superadmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
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

        $attributes['logo'] = UploadedFile::fake()->image('logo.jpg')->size(1000);
        $attributes['plan_id'] = $plan->id;

        $this->post(route('superadmin.institutes.store'), $attributes)
            ->assertRedirect(route('superadmin.institutes.index'));

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

        Storage::disk('public')->assertExists("logos/{$institute->logo}");

        $this->get(route('superadmin.institutes.index'))
            ->assertSee($attributes['name']);
    }
}
