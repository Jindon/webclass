<?php

namespace App\Http\Controllers\Superadmin\Auth;

use App\Http\Controllers\Controller;
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

    public function showLoginForm()
    {
        return view('auth.login',[
            'title' => 'Superadmin Login',
            'loginRoute' => 'superadmin.login',
            'forgotPasswordRoute' => 'superadmin.password.request',
        ]);
    }

    public function login(Request $request)
    {
        $this->validator($request);

        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }


        if($this->guard()->attempt($request->only('email','password'),$request->filled('remember'))){
            //Authentication passed...
            return redirect()
                ->intended(route('superadmin.home'))
                ->with('status','You are Logged in as Superadmin!');
        }

        $this->incrementLoginAttempts($request);

        //Authentication failed...
        return $this->loginFailed();
    }

    public function logout()
    {
        $this->guard()->logout();
        return redirect()
            ->route('superadmin.login')
            ->with('status','Superadmin has been logged out!');
    }


    private function validator(Request $request)
    {
        //validation rules.
        $rules = [
            'email'    => 'required|email|exists:superadmins|min:5|max:191',
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
        return Auth::guard('superadmin');
    }
}
