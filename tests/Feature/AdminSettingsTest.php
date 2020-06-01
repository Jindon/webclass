<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Institute;
use App\Models\InstitutePlan;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_see_related_institute_settings() {
        $institute = factory(Institute::class)->create(['subdomain' => 'institute']);
        $admin = $this->signIn('admin', $institute);

        $this->get(route('admin.settings.index', ['subdomain' => 'institute']))
            ->assertViewHas(['institute' => $institute, 'admin' => $admin]);
    }

    /** @test */
    public function admin_can_update_related_institute_settings() {
        Storage::fake('public');
        $institute = factory(Institute::class)->create(['subdomain' => 'institute']);
        $admin = factory(Admin::class)->create(['institute_id' => $institute->id]);

        $this->signIn('admin', null, $admin);

        $attributes = factory(Institute::class)->raw();
        $admin_attributes = factory(Admin::class)->raw([
            'name' => 'Updated name',
            'phone' => '8796541230',
            'old_password' => 'welcome',
            'password' => 'welcome_new',
            'password_confirmation' => 'welcome_new'
        ]);

        $attributes['logo'] = UploadedFile::fake()->image('logo.jpg')->size(1000);
        $attributes['admin'] = $admin_attributes;

        $this->patch(route('admin.settings.update', ['subdomain' => 'institute']), Arr::except($attributes, ['subdomain']))
            ->assertRedirect(route('admin.settings.index', ['subdomain' => 'institute']))
            ->assertSessionHas(['message' => 'Settings updated successfully!']);

        $this->assertDatabaseHas('institutes', [
            'id' => $institute->id,
            'name' => $attributes['name'],
            'board' => $attributes['board'],
        ]);

        $this->assertDatabaseHas('admins', [
            'id' => $admin->id,
            'institute_id' => $institute->id,
            'name' => $admin_attributes['name'],
            'phone' => $admin_attributes['phone'],
        ]);
        Storage::disk('public')->assertExists("logos/{$institute->logo}");

        $this->assertTrue(Hash::check('welcome_new', $institute->admin->password), 'Password is not updated correctly!');
    }
}
