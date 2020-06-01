<?php

namespace Tests;

use App\Models\Admin;
use App\Models\Superadmin;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function signIn($type, $institute = null, $user = null)
    {
        switch ($type) {
            case 'superadmin':
                $this->actingAs($user ?? factory(Superadmin::class)->create(), 'superadmin');
                break;
            case 'admin':
                $this->actingAs($user ?? factory(Admin::class)->create([
                    'institute_id' => $institute->id
                    ]), 'admin');
                break;
            default:
                $this->actingAs($user ?? factory(Superadmin::class)->create(), 'superadmin');
        }
    }
}
