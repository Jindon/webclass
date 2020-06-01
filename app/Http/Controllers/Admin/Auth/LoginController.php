<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Institute;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use ThrottlesLogins;

    /**
     * The maximum number of attempts to allow.
     *
     * @return int
     */
    protected $maxAttempts = 7;


    /**
     * The number of minutes to throttle for.
     *
     * @return int
     */
    protected $decayMinutes = 1;

    public function showLoginForm(string $subdomain)
    {
        return view('auth.login',[
            'title' => 'Admin Login',
            'loginRoute' => 'admin.login',
            'forgotPasswordRoute' => 'admin.password.request',
        ]);
    }

    public function login(Request $request, string $subdomain)
    {
        $this->validator($request);

        $institute = Institute::whereSubdomain($subdomain)->first();
        $admin = Admin::whereEmail($request->email)->first();
        if($admin->institute_id != $institute->id) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'The given email is not the admin email!']);
        }

        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }


        if($this->guard()->attempt($request->only('email','password'),$request->filled('remember'))){
            //Authentication passed...
            return redirect()
                ->intended(route('admin.home'))
                ->with('status','You are Logged in as Admin!');
        }

        $this->incrementLoginAttempts($request);

        //Authentication failed...
        return $this->loginFailed();
    }

    public function logout()
    {
        $this->guard()->logout();
        return redirect()
            ->route('admin.login')
            ->with('status','Admin has been logged out!');
    }


    private function validator(Request $request)
    {
        //validation rules.
        $rules = [
            'email'    => 'required|email|exists:admins|min:5|max:191',
            'password' => 'required|string|min:4|max:255',
        ];

        //custom validation error messages.
        $messages = [
            'email.exists' => 'These credentials do not match our records.',
        ];

        //validate the request.
        $request->validate($rules, $messages);
    }

    private function loginFailed()
    {
        return redirect()
            ->back()
            ->withInput()
            ->with('error','Login failed, please try again!');
    }

    protected function username()
    {
        return 'email';
    }

    private function guard()
    {
        return Auth::guard('admin');
    }
}
