<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlansController extends Controller
{
    public function index()
    {
        $plans = Plan::get();
        return view('superadmin.plans.index')->with([
            'plans' => $plans
        ]);
    }
    public function store(Request $request)
    {
        $this->validator($request);

        Plan::create($request->all());

        return redirect()->route('superadmin.plans.index');
    }

    public function update(Request $request, Plan $plan)
    {
        $this->validator($request);

        $plan->update($request->all());

        return redirect()->route('superadmin.plans.index')->with([
            'message' => 'Plan updated successfully!'
        ]);
    }

    public function delete(Plan $plan)
    {
        $plan->delete();

        return redirect()->route('superadmin.plans.index')->with([
            'message' => 'Plan deleted successfully!'
        ]);
    }

    private function validator(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required|boolean',
            'max_students' => 'required|numeric',
            'max_uploads' => 'required|numeric',
            'price' => 'required|numeric',
        ]);
    }
}
