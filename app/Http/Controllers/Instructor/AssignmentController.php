<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Template;
use App\Models\Course;
use App\Models\Assignment_Annotation;
use Illuminate\Http\Request;
use App\Models\Submission;
use Illuminate\Support\Facades\Log;

class AssignmentController extends Controller
{
    public function index($courseNo)
    {
        $course = Course::where('id', $courseNo)->firstOrFail();
        $assignments = Assignment::where('course_number', $course->course_number)->get();
        session(['last_opened_course' => $courseNo]);

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
            'assignment_type' => 'required|string|in:Quiz,Bubble,Homework,Online,Programming',
        ]);        

        $path = $request->file('template_pdf')->store('templates', 'public');
        $template = Template::create([
            'file_path' => $path,
        ]);

        $course_id = session('last_opened_course');
        $course = Course::where('id', $course_id)->firstOrFail();

        $type    = $request->input('assignment_type');
        $release = $request->input('release_date');
        $assignment = Assignment::create([
            'Name'              => $request->input('assignment_name'),
            'course_number'     => $course->course_number,
            'points'            => 0,
            'release_date'      => $request->input('release_date'),
            'due_date'          => $type === 'Quiz' ? $release : $request->input('due_date'),
            'status'            => 'Edit Outline',
            'submissions_count' => 0,
            'template_id'       => $template->id,
            'type'              => $type, 
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
            if (!is_array($annotations)) {
                continue; // Skip if annotations are null or invalid
            }

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

    public function annotateTemplate($assignmentId)
    {
        $assignment   = Assignment::findOrFail($assignmentId);
        $template     = Template::findOrFail($assignment->template_id);
    
        $annotations = Assignment_Annotation::where('assignment_id', $assignmentId)
        ->get()
        ->groupBy('page') // This is the correct field now
        ->map(function ($group, $page) {
            return $group->map(function ($annotation) {
                return [
                    'top'    => $annotation->top,
                    'left'   => $annotation->left,
                    'width'  => $annotation->width,
                    'height' => $annotation->height,
                    'name'   => $annotation->name,
                ];
            })->values();
        });
    
    
        return view('assignments.annotate-template', [
            'templateId'   => $template->id,
            'filePath'     => $template->file_path,
            'annotations'  => $annotations, // must be passed as JSON-like
        ]);
    }
    
    public function uploadForm($assignmentId)
    {
        return view('assignments.upload-submission', compact('assignmentId'));
    }

    public function upload(Request $request, $assignmentId)
    {
        $request->validate([
            'submission_file' => 'required|file|max:20480', // max 10MB
        ]);
    
        $path = $request->file('submission_file')->store('submissions');
    
        Submission::create([
            'assignment_id' => $assignmentId,
            'file_path' => $path,
        ]);

        $assignment = Assignment::findOrFail($assignmentId);
        $assignment->status = 'Submission Uploaded';
        $assignment->save();
    
        $course_id = session('last_opened_course');
        return redirect()->route('assignments.index',$course_id)->with('success', 'Submission uploaded successfully.');
    }

    public function edit($id)
    {
        $assignment = Assignment::findOrFail($id);
        return view('assignments.edit', compact('assignment'));
    }

    /**
     * Handle the update.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'Name'         => 'required|string|max:255',
            'release_date' => 'nullable|date',
        ]);

        $assignment = Assignment::findOrFail($id);
        $assignment->Name         = $request->input('Name');
        $assignment->release_date = $request->input('release_date');
        $assignment->due_date     = $request->input('release_date');
        $assignment->save();

        return redirect()
            ->route('assignments.index', session('last_opened_course'))
            ->with('success', 'Assignment updated.');
    }

    /**
     * Delete the assignment.
     */
    public function deleteAssignment($id)
    {
        $assignment = Assignment::findOrFail($id);
        $template = Template::findOrFail($assignment->template_id);

        $assignment->delete();
        $template->delete();

        return redirect()
            ->route('assignments.index', session('last_opened_course'))
            ->with('success', 'Assignment deleted.');
    }
    
}
