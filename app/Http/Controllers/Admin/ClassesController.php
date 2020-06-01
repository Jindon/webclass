<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Iclass;
use App\Models\IclassSection;
use App\Models\Section;
use Illuminate\Http\Request;

class ClassesController extends Controller
{
    private $institute;

    public function __construct()
    {
        $this->institute = auth()->user()->institute;
    }

    public function index()
    {
        $sections = $this->institute->sections;
        $classes = $this->institute->iclasses;
        return view('admin.classes.index')->with([
            'sections' => $sections,
            'classes' => $classes,
        ]);
    }

    public function store(Request $request, string $subdomain)
    {
        $this->validator($request);

        try {
            $iclass = Iclass::create([
                'name' => $request->name,
                'description' => $request->description,
                'institute_id' => $this->institute->id,
            ]);

            if ($request->has('sections')) {
                foreach ($request->sections as $section) {
                    if (Section::findOrFail($section)->institute_id == $this->institute->id) {
                        IclassSection::create([
                            'institute_id' => $this->institute->id,
                            'iclass_id' => $iclass->id,
                            'section_id' => $section,
                        ]);
                    }
                }
            }

            return redirect()->route('admin.classes.index')->with([
                'message' => 'Class added successfully!'
            ]);
        } catch (\Exception $error) {
            abort(503, $error->getMessage());
        }
    }

    public function update(Request $request, string $subdomain, Iclass $iclass)
    {
        $this->authorize('manage', $iclass);

        $this->validator($request);

        try {
            $iclass->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            if ($request->has('sections')) {
                /**  Delete existing class section mapping */
                IclassSection::whereInstituteId($this->institute->id)->whereIclassId($iclass->id)->delete();

                foreach ($request->sections as $section) {
                    if (Section::findOrFail($section)->institute_id == $this->institute->id) {
                        IclassSection::create([
                            'institute_id' => $this->institute->id,
                            'iclass_id' => $iclass->id,
                            'section_id' => $section,
                        ]);
                    }
                }
            }

            return redirect()->route('admin.classes.index')->with([
                'message' => 'Class updated successfully!'
            ]);
        } catch (\Exception $error) {
            abort(503, $error->getMessage());
        }
    }

    public function delete(string $subdomain, Iclass $iclass)
    {
        $this->authorize('manage', $iclass);

        $iclass->delete();

        return redirect()->route('admin.classes.index')->with([
            'message' => 'Class deleted successfully!'
        ]);
    }

    private function validator(Request $request)
    {
        $request->validate([
            'name' => 'required|max:32',
            'description' => 'required|max:124',
        ]);
    }
}
