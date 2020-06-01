<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index(string $subdomain)
    {
        $institute = auth()->user()->institute;
        return view('admin.settings.index')->with([
            'institute' => $institute,
            'admin' => $institute->admin
        ]);
    }

    public function update(Request $request)
    {
        $this->validator($request);
        $institute = auth()->user()->institute;

        if ($request->hasFile('logo')) {
            $institute->addLogo($request->file('logo'));
        }

        if ($request->has('admin.password')) {
            $correct_password = Hash::check($request->admin['old_password'], auth()->user()->password);
            if ($correct_password) {
                auth()->user()->updatePassword($request->admin['password']);
            }
        }

        $institute->update($request->except('admin', 'subdomain'));
        auth()->user()->update([
            'name' => $request->admin['name'],
            'country_code' => $request->admin['country_code'],
            'phone' => $request->admin['phone'],
        ]);

        return redirect()->route('admin.settings.index')->with([
            'message' => 'Settings updated successfully!'
        ]);

    }

    private function validator(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'board' => 'required',
            'logo' => 'nullable|image|max:5120|mimes:jpeg,png',
            'admin.name' => 'required',
            'admin.country_code' => 'nullable|digits:2',
            'admin.phone' => 'nullable|digits:10',
            'admin.password' => 'sometimes|confirmed|min:6',
            'admin.old_password' => 'required_with:admin.password'
        ]);
    }
}
