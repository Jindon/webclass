<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Institute;
use App\Models\InstitutePlan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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
                'name', 'board'
            ]));

            if ($request->hasFile('logo')) {
                $institute->addLogo($request->file('logo'));
            }

            InstitutePlan::updateOrCreate([
                'institute_id' => $institute->id,
                'plan_id' => $request->plan_id,
                'start_date' => Carbon::now()->toDate(),
                'end_date' => Carbon::now()->addYear()->toDate(),
            ]);

            return redirect()->route('superadmin.institutes.index');
        } catch (\Exception $error) {
            abort(503);
        }
    }
    private function validator(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'board' => 'required',
            'logo' => 'image|max:5120|mimes:jpeg,png',
            'plan_id' => 'integer'
        ]);
    }
}
