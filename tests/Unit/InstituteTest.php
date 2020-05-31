<?php

namespace Tests\Unit;

use App\Models\Institute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstituteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_a_path()
    {
        $institute = factory(Institute::class)->create();

        $this->assertEquals('/institutes/' . $institute->id, $institute->path());
    }

}
