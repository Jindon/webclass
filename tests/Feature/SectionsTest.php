<?php

namespace Tests\Feature;

use App\Models\Institute;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SectionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_see_all_sections_for_the_institute_only()
    {
        $institute = factory(Institute::class)->create(['subdomain' => 'institute']);
        $another_institute = factory(Institute::class)->create(['subdomain' => 'another_institute']);
        $this->signIn('admin', $institute);

        $sectionA = factory(Section::class)->create(['institute_id' => $institute->id]);
        $sectionB = factory(Section::class)->create(['institute_id' => $institute->id]);
        $sectionC = factory(Section::class)->create(['institute_id' => $another_institute->id]);

        $this->get(route('admin.classes.index', ['subdomain' => 'institute']))
            ->assertStatus(200)
            ->assertSee($sectionA->name)
            ->assertSee($sectionB->name)
            ->assertDontSee($sectionC->name);
    }

    /** @test */
    public function admin_can_create_a_section_for_the_institute()
    {
        $institute = factory(Institute::class)->create(['subdomain' => 'institute']);
        $this->signIn('admin', $institute);

        $attributes = factory(Section::class)->raw();

        $this->post(route('admin.sections.store', ['subdomain' => 'institute']), $attributes)
            ->assertRedirect(route('admin.classes.index'))
            ->assertSessionHas(['message' => 'Section added successfully!']);

        $this->assertDatabaseHas('sections', [
            'name' => $attributes['name'],
            'institute_id' => $institute->id
        ]);

        $this->get(route('admin.classes.index', ['subdomain' => 'institute']))
            ->assertSee($attributes['name']);
    }

    /** @test */
    public function admin_can_update_a_subject_for_the_institute()
    {
        $institute = factory(Institute::class)->create(['subdomain' => 'institute']);
        $this->signIn('admin', $institute);

        $section = factory(Section::class)->create(['institute_id' => $institute->id]);
        $attributes = ['name' => 'Updated Name'];

        $this->patch(route('admin.sections.update', ['subdomain' => 'institute', 'section' => $section]), $attributes)
            ->assertRedirect(route('admin.classes.index'))
            ->assertSessionHas(['message' => 'Section updated successfully!']);

        $this->assertDatabaseHas('sections', [
            'id' => $section->id,
            'name' => $attributes['name'],
        ]);
    }

    /** @test */
    public function admin_cannot_update_a_section_for_another_institute()
    {
        $institute = factory(Institute::class)->create(['subdomain' => 'institute']);
        $this->signIn('admin', $institute);
        $another_institute = factory(Institute::class)->create(['subdomain' => 'another_institute']);

        $section = factory(Section::class)->create(['institute_id' => $another_institute->id]);
        $attributes = factory(Section::class)->raw();

        $this->patch(route('admin.sections.update', ['subdomain' => 'institute', 'section' => $section]), $attributes)
            ->assertStatus(403);
    }

    /** @test */
    public function admin_can_delete_a_section_for_the_institute()
    {
        $institute = factory(Institute::class)->create(['subdomain' => 'institute']);
        $this->signIn('admin', $institute);

        $section = factory(Section::class)->create(['institute_id' => $institute->id]);

        $this->delete(route('admin.sections.delete', ['subdomain' => 'institute', 'section' => $section]))
            ->assertRedirect(route('admin.classes.index'))
            ->assertSessionHas(['message' => 'Section deleted successfully!']);

        $this->assertDeleted($section);
    }

    /** @test */
    public function admin_cannot_delete_a_section_of_another_institute()
    {
        $institute = factory(Institute::class)->create(['subdomain' => 'institute']);
        $another_institute = factory(Institute::class)->create(['subdomain' => 'another_institute']);
        $this->signIn('admin', $institute);

        $section = factory(Section::class)->create(['institute_id' => $another_institute->id]);

        $this->delete(route('admin.sections.delete', ['subdomain' => 'institute', 'section' => $section]))
            ->assertStatus(403);
    }
}
