<?php

namespace Tests\Feature;

use App\Models\Institute;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubjectsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_see_all_subjects_for_the_institute_only()
    {
        $institute = factory(Institute::class)->create(['subdomain' => 'institute']);
        $another_institute = factory(Institute::class)->create(['subdomain' => 'another']);
        $this->signIn('admin', $institute);

        $english = factory(Subject::class)->create(['institute_id' => $institute->id]);
        $physics = factory(Subject::class)->create(['institute_id' => $institute->id]);
        $biology = factory(Subject::class)->create(['institute_id' => $another_institute->id]);

        $this->get(route('admin.subjects.index', ['subdomain' => 'institute']))
            ->assertStatus(200)
            ->assertSee($english->name)
            ->assertSee($physics->name)
            ->assertDontSee($biology->name);
    }

    /** @test */
    public function admin_can_create_a_subject_for_the_institute()
    {
        $institute = factory(Institute::class)->create(['subdomain' => 'institute']);
        $this->signIn('admin', $institute);

        $attributes = factory(Subject::class)->raw();

        $this->post(route('admin.subjects.store', ['subdomain' => 'institute']), $attributes)
            ->assertRedirect(route('admin.subjects.index'))
            ->assertSessionHas(['message' => 'Subject added successfully!']);

        $this->assertDatabaseHas('subjects', [
            'name' => $attributes['name'],
            'institute_id' => $institute->id
        ]);

        $this->get(route('admin.subjects.index', ['subdomain' => 'institute']))
            ->assertSee($attributes['name']);
    }

    /** @test */
    public function admin_can_update_a_subject_for_the_institute()
    {
        $institute = factory(Institute::class)->create(['subdomain' => 'institute']);
        $this->signIn('admin', $institute);

        $subject = factory(Subject::class)->create(['institute_id' => $institute->id]);
        $attributes = factory(Subject::class)->raw();

        $this->patch(route('admin.subjects.update', ['subdomain' => 'institute', 'subject' => $subject]), $attributes)
            ->assertRedirect(route('admin.subjects.index'))
            ->assertSessionHas(['message' => 'Subject updated successfully!']);

        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'name' => $attributes['name'],
            'abbreviation' => $attributes['abbreviation'],
        ]);
    }

    /** @test */
    public function admin_cannot_update_a_subject_for_another_institute()
    {
        $institute = factory(Institute::class)->create(['subdomain' => 'institute']);
        $this->signIn('admin', $institute);
        $another_institute = factory(Institute::class)->create(['subdomain' => 'another_institute']);

        $subject = factory(Subject::class)->create(['institute_id' => $another_institute->id]);
        $attributes = factory(Subject::class)->raw();

        $this->patch(route('admin.subjects.update', ['subdomain' => 'institute', 'subject' => $subject]), $attributes)
            ->assertStatus(403);
    }

    /** @test */
    public function admin_can_delete_a_subject_for_the_institute()
    {
        $this->withoutExceptionHandling();
        $institute = factory(Institute::class)->create(['subdomain' => 'institute']);
        $this->signIn('admin', $institute);

        $subject = factory(Subject::class)->create(['institute_id' => $institute->id]);

        $this->delete(route('admin.subjects.delete', ['subdomain' => 'institute', 'subject' => $subject]))
            ->assertRedirect(route('admin.subjects.index'))
            ->assertSessionHas(['message' => 'Subject deleted successfully!']);

        $this->assertDeleted($subject);
    }

    /** @test */
    public function admin_cannot_delete_a_subject_of_another_institute()
    {
        $institute = factory(Institute::class)->create(['subdomain' => 'institute']);
        $another_institute = factory(Institute::class)->create(['subdomain' => 'another_institute']);
        $this->signIn('admin', $institute);

        $subject = factory(Subject::class)->create(['institute_id' => $another_institute->id]);

        $this->delete(route('admin.subjects.delete', ['subdomain' => 'institute', 'subject' => $subject]))
            ->assertStatus(403);
    }
}
