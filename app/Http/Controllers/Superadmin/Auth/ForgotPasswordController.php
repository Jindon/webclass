<?php

namespace App\Http\Controllers\Superadmin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;
    /**
     * Only guests for "admin" guard are allowed except
     * for logout.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:superadmin');
    }

    public function showLinkRequestForm(){
        return view('auth.passwords.email',[
            'title' => 'Superadmin Password Reset',
            'passwordEmailRoute' => 'superadmin.password.email'
        ]);
    }

    public function broker(){
        return Password::broker('superadmins');
    }

    public function guard(){
        return Auth::guard('superadmin');
    }
}
