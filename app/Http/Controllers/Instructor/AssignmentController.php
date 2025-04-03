<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Template;
use App\Models\Course;
use App\Models\Assignment_Annotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AssignmentController extends Controller
{
    public function index($courseNo)
    {
        $course = Course::where('id', $courseNo)->firstOrFail();
        $assignments = Assignment::where('course_number', $course->course_number)->get();

        Log::info('Assignments Data:', $assignments->toArray());
        return view('assignments.index', compact( 'assignments'));
    }

    public function create($course_id)
    {
        $course = Course::where('id', $course_id)->firstOrFail();
        return view('assignments.create', compact('course_id'));
    }

    public function storeTemplate(Request $request)
    {
        $request->validate([
            'template_pdf'    => 'required|mimes:pdf|max:51200',
            'assignment_name' => 'required|string|max:255',
            'points'          => 'nullable|numeric',
            'release_date'    => 'nullable|date',
            'due_date'        => 'nullable|date',
        ]);        

        $path = $request->file('template_pdf')->store('templates', 'public');
        $template = Template::create([
            'file_path' => $path,
            'is_temporary'     => true,
        ]);

        $course_id = session('last_opened_course');
        $course = Course::where('id', $course_id)->firstOrFail();

        $assignment = Assignment::create([
            'Name'              => $request->input('assignment_name'),
            'course_number'     => $course->course_number,
            'points'            => $request->input('points'),
            'release_date'      => $request->input('release_date'),
            'due_date'          => $request->input('due_date'),
            'status'            => false,
            'submissions_count' => 0,
            'template_id'       => $template->id,
            'type'              => 'quiz', 
        ]);

        session(['current_assignment_id' => $assignment->id]);

        return view('assignments.annotate-template', [
            'templateId' => $template->id,
            'filePath' => $template->file_path,
        ]);
    }

    public function saveAnnotation(Request $request)
    {
        $data = $request->json()->all();

        $assignment_id = session('current_assignment_id');
        Assignment_Annotation::where('assignment_id', $assignment_id)->delete();

        foreach ($data as $page => $annotations) {
            foreach ($annotations as $annotationData) {
                Assignment_Annotation::create([
                    'assignment_id' => $assignment_id,
                    'page'  => (int)$page,
                    'top'   => $annotationData['top'],
                    'left'  => $annotationData['left'],
                    'width' => $annotationData['width'],
                    'height'=> $annotationData['height'],
                    'name'  => $annotationData['name'],
                ]);
            }
        }

        $course_id = session('last_opened_course');

        return response()->json([
            'success' => true,
            'redirect_url' => route('assignments.index', $course_id)
        ]);
        
    }

}
