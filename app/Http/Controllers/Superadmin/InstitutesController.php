<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Institute;
use App\Models\InstitutePlan;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class InstitutesController extends Controller
{
    public function index()
    {
        $institutes = Institute::get();

        return view ('superadmin.institutes.index')->with([
            'institutes' => $institutes
        ]);
    }
    public function store(Request $request)
    {
        $this->validator($request);

        try {
            $institute = Institute::create($request->only([
                'name', 'board', 'subdomain'
            ]));

            if ($request->hasFile('logo')) {
                $institute->addLogo($request->file('logo'));
            }

            InstitutePlan::create([
                'institute_id' => $institute->id,
                'plan_id' => $request->plan_id,
                'start_date' => Carbon::now()->toDate(),
                'end_date' => Carbon::now()->addYear()->toDate(),
            ]);

            Admin::create($request->admin);

            return redirect()->route('superadmin.institutes.index')->with([
                'message' => 'Institute created successfully!'
            ]);
        } catch (\Exception $error) {
            abort(503);
        }
    }

    public function update(Request $request, Institute $institute)
    {
        $this->validator($request, 'update', $institute);

        if ($request->hasFile('logo')) {
            $institute->addLogo($request->file('logo'));
        }

        $institute->update($request->except('admin', 'plan_id'));

        if ($request->has('admin')) {
            $institute->admin()->update(Arr::except($request->admin, [
                'password', 'password_confirmation'
            ]));
            if(! empty($request->admin->password)) {
                $institute->admin->password = Hash::make($request->password);
                $institute->save();
            }
        }

        if (! empty($institute->plan)) {
            $institute->plan()->update([
                'institute_id' => $institute->id,
                'plan_id' => $request->plan_id,
            ]);
        }

        return redirect()->route('superadmin.institutes.index')->with([
            'message' => 'Institute updated successfully!'
        ]);
    }

    public function manageSubscription(Request $request, Institute $institute)
    {
        $institute->plan()->update($request->only('start_date','end_date'));

        return redirect()->route('superadmin.institutes.index')->with([
            'message' => "Institute's subscription updated successfully!"
        ]);
    }

    public function delete(Institute $institute)
    {
        $institute->delete();

        return redirect()->route('superadmin.institutes.index')->with([
            'message' => "Institute deleted successfully!"
        ]);
    }

    private function validator(Request $request, $type = 'create', Institute $institute = null)
    {
        $rules = [
            'name' => 'required',
            'board' => 'required',
            'logo' => 'nullable|image|max:5120|mimes:jpeg,png',
            'plan_id' => 'required|integer',
            'admin.name' => 'required',
            'admin.email' => 'email|required',
            'admin.country_code' => 'nullable|digits:2',
            'admin.phone' => 'nullable|digits:10',
        ];
        switch ($type) {
            case 'create':
                $request->validate(array_merge($rules, [
                    'admin.password' => 'required|min:6|confirmed',
                    'subdomain' => 'required|unique:institutes,subdomain',
                ]));
                break;
            case 'update':
                $request->validate(array_merge($rules, [
                    'admin.password' => 'sometimes|min:6|confirmed',
                    'subdomain' => 'required|unique:institutes,subdomain' . $institute->id,
                ]));
                break;
            default:
                $request->validate($rules);
        }
    }
}
