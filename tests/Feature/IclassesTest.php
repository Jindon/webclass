<?php

namespace Tests\Feature;

use App\Models\Iclass;
use App\Models\IclassSection;
use App\Models\Institute;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IclassesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_create_a_class_for_the_institute()
    {
        $institute = factory(Institute::class)->create(['subdomain' => 'institute']);
        $this->signIn('admin', $institute);

        $sectionA = factory(Section::class)->create(['institute_id' => $institute->id]);
        $sectionB = factory(Section::class)->create(['institute_id' => $institute->id]);

        $attributes = factory(Iclass::class)->raw([
            'sections' => [$sectionA->id, $sectionB->id]
        ]);

        $this->post(route('admin.classes.store', ['subdomain' => 'institute']), $attributes)
            ->assertRedirect(route('admin.classes.index'))
            ->assertSessionHas(['message' => 'Class added successfully!']);

        $this->assertDatabaseHas('iclasses', [
            'name' => $attributes['name'],
            'description' => $attributes['description'],
            'institute_id' => $institute->id
        ]);

        $class = Iclass::first();

        $this->assertDatabaseHas('iclass_sections', [
            'institute_id' => $institute->id,
            'iclass_id' => $class->id,
            'section_id' => $sectionA->id,
        ]);
        $this->assertDatabaseHas('iclass_sections', [
            'institute_id' => $institute->id,
            'iclass_id' => $class->id,
            'section_id' => $sectionB->id,
        ]);

        $this->get(route('admin.classes.index', ['subdomain' => 'institute']))
            ->assertSee($attributes['name'])
            ->assertSee($sectionA['name']);
    }

    /** @test */
    public function admin_can_update_a_class_for_the_institute()
    {
        $institute = factory(Institute::class)->create(['subdomain' => 'institute']);
        $this->signIn('admin', $institute);

        $sectionA = factory(Section::class)->create(['institute_id' => $institute->id]);
        $sectionB = factory(Section::class)->create(['institute_id' => $institute->id]);
        $sectionC = factory(Section::class)->create(['institute_id' => $institute->id]);

        $class = factory(Iclass::class)->create(['institute_id' => $institute->id]);
        $class_sectionA = IclassSection::create([
            'institute_id' => $institute->id,
            'iclass_id' => $class->id,
            'section_id' => $sectionA->id
        ]);
        $class_sectionB = IclassSection::create([
            'institute_id' => $institute->id,
            'iclass_id' => $class->id,
            'section_id' => $sectionB->id
        ]);
        $attributes = factory(Iclass::class)->raw([
            'sections' => [$sectionA->id, $sectionC->id]
        ]);

        $this->patch(route('admin.classes.update', ['subdomain' => 'institute', 'iclass' => $class]), $attributes)
            ->assertRedirect(route('admin.classes.index'))
            ->assertSessionHas(['message' => 'Class updated successfully!']);

        $this->assertDatabaseHas('iclasses', [
            'id' => $class->id,
            'name' => $attributes['name'],
            'description' => $attributes['description'],
            'institute_id' => $institute->id
        ]);

        $this->assertDatabaseHas('iclass_sections', [
            'institute_id' => $institute->id,
            'iclass_id' => $class->id,
            'section_id' => $sectionA->id,
        ]);
        $this->assertDatabaseHas('iclass_sections', [
            'institute_id' => $institute->id,
            'iclass_id' => $class->id,
            'section_id' => $sectionC->id,
        ]);

        $this->assertDeleted($class_sectionB);
    }

    /** @test */
    public function admin_cannot_update_a_class_for_another_institute()
    {
        $institute = factory(Institute::class)->create(['subdomain' => 'institute']);
        $another_institute = factory(Institute::class)->create(['subdomain' => 'another_institute']);
        $this->signIn('admin', $institute);

        $class = factory(Iclass::class)->create(['institute_id' => $another_institute->id]);
        $attributes = factory(Iclass::class)->raw();

        $this->patch(route('admin.classes.update', ['subdomain' => 'institute', 'iclass' => $class]), $attributes)
            ->assertStatus(403);
    }

    /** @test */
    public function admin_can_delete_a_class_for_the_institute()
    {
        $institute = factory(Institute::class)->create(['subdomain' => 'institute']);
        $this->signIn('admin', $institute);

        $class = factory(Iclass::class)->create(['institute_id' => $institute->id]);
        $sectionA = factory(Section::class)->create(['institute_id' => $institute->id]);
        $sectionB = factory(Section::class)->create(['institute_id' => $institute->id]);

        $class_sectionA = IclassSection::create([
            'institute_id' => $institute->id,
            'iclass_id' => $class->id,
            'section_id' => $sectionA->id
        ]);
        $class_sectionB = IclassSection::create([
            'institute_id' => $institute->id,
            'iclass_id' => $class->id,
            'section_id' => $sectionB->id
        ]);

        $this->delete(route('admin.classes.delete', ['subdomain' => 'institute', 'iclass' => $class]))
            ->assertRedirect(route('admin.classes.index'))
            ->assertSessionHas(['message' => 'Class deleted successfully!']);

        $this->assertDeleted($class);
        $this->assertDeleted($class_sectionA);
        $this->assertDeleted($class_sectionB);
    }

    /** @test */
    public function admin_cannot_delete_a_class_of_another_institute()
    {
        $institute = factory(Institute::class)->create(['subdomain' => 'institute']);
        $another_institute = factory(Institute::class)->create(['subdomain' => 'another_institute']);
        $this->signIn('admin', $institute);

        $class = factory(Iclass::class)->create(['institute_id' => $another_institute->id]);

        $this->delete(route('admin.classes.delete', ['subdomain' => 'institute', 'iclass' => $class]))
            ->assertStatus(403);
    }
}
