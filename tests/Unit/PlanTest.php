<?php

namespace Tests\Unit;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_a_path()
    {
        $plan = factory(Plan::class)->create();

        $this->assertEquals('/plans/' . $plan->id, $plan->path());
    }
}
