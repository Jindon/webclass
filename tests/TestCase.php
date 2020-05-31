<?php

namespace Tests;

use App\Models\Superadmin;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function signIn($type, $user = null)
    {
        switch ($type) {
            case 'superadmin':
                $this->actingAs($user ?? factory(Superadmin::class)->create(), 'superadmin');
                break;
            default:
                $this->actingAs($user ?? factory(Superadmin::class)->create(), 'superadmin');
        }
    }
}
