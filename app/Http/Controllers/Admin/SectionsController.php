<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionsController extends Controller
{
    private $institute;

    public function __construct()
    {
        $this->institute = auth()->user()->institute;
    }
    public function store(Request $request)
    {
        $this->validator($request);

        Section::create([
            'name' => $request->name,
            'institute_id' => $this->institute->id
        ]);

        return redirect()->route('admin.classes.index')->with([
            'message' => 'Section added successfully!'
        ]);
    }

    public function update(Request $request, string $subdomain, Section $section)
    {
        $this->authorize('manage', $section);

        $this->validator($request);

        $section->update($request->only('name'));
        return redirect()->route('admin.classes.index')->with([
            'message' => 'Section updated successfully!'
        ]);
    }

    public function delete(string $subdomain, Section $section)
    {
        $this->authorize('manage', $section);

        $section->delete();

        return redirect()->route('admin.classes.index')->with([
            'message' => 'Section deleted successfully!'
        ]);
    }

    private function validator(Request $request)
    {
        $request->validate([
            'name' => 'required|max:32',
        ]);
    }
}
