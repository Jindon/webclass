<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectsController extends Controller
{
    private $institute;

    public function __construct()
    {
        $this->institute = auth()->user()->institute;
    }
    public function index()
    {
        $subjects = Subject::whereInstituteId($this->institute->id)->get();

        return view('admin.subjects.index')->with([
            'subjects' => $subjects
        ]);
    }

    public function store(Request $request)
    {
        $this->validator($request);

        Subject::create([
            'name' => $request->name,
            'abbreviation' => $request->abbreviation,
            'institute_id' => $this->institute->id
        ]);

        return redirect()->route('admin.subjects.index')->with([
            'message' => 'Subject added successfully!'
        ]);
    }

    public function update(Request $request, string $subdomain, Subject $subject)
    {
        $this->authorize('manage', $subject);

        $this->validator($request);
        $subject->update([
            'name' => $request->name,
            'abbreviation' => $request->abbreviation,
        ]);

        return redirect()->route('admin.subjects.index')->with([
            'message' => 'Subject updated successfully!'
        ]);
    }

    public function delete(string $subdomain, Subject $subject)
    {
        $this->authorize('manage', $subject);

        $subject->delete();

        return redirect()->route('admin.subjects.index')->with([
            'message' => 'Subject deleted successfully!'
        ]);
    }

    private function validator(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'abbreviation' => 'required|max:6',
        ]);
    }
}
