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
            'status'            => 'Assignment Created',
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

    public function uploadForm($assignmentId)
    {
        return view('assignments.upload-submission', compact('assignmentId'));
    }
    
    // public function upload(Request $request, $assignmentId)
    // {
    //     $request->validate([
    //         'submission_file' => 'required|file|max:10000',
    //     ]);
    
    //     $path = $request->file('submission_file')->store('submissions');
    //     $fullPath = storage_path('app/' . $path);
    
    //     Log::info('Uploaded file stored at:', ['path' => $fullPath]);
    
    //     $imagePath = $fullPath;
    
    //     // Step 1: Convert PDF to image (only first page)
    //     if (str_ends_with(strtolower($path), '.pdf')) {
    //         $imagePath = str_replace('.pdf', '.jpg', $fullPath);
    //         $convertCmd = "convert -density 300 \"$fullPath[0]\" -quality 100 \"$imagePath\"";
    //         exec($convertCmd . ' 2>&1', $convertOutput);
    
    //         Log::info('PDF to image conversion command executed.', [
    //             'command' => $convertCmd,
    //             'output' => $convertOutput,
    //         ]);
    
    //         if (!file_exists($imagePath)) {
    //             Log::error('PDF to image conversion failed.', ['imagePath' => $imagePath]);
    //             return back()->with('error', 'Failed to convert PDF to image.');
    //         }
    //     }
    
    //     $rollNo = null;
    //     $dept = null;
    
    //     // Step 2: Get annotations
    //     $annotations = Assignment_Annotation::where('assignment_id', $assignmentId)->get();
    //     Log::info('Annotation count:', ['count' => $annotations->count()]);
    
    //     foreach ($annotations as $annotation) {
    //         $name = strtolower(str_replace(' ', '_', $annotation->name));
    //         $geometry = "{$annotation->width}x{$annotation->height}+{$annotation->left}+{$annotation->top}";
    //         $croppedPath = storage_path("app/crop_{$name}.jpg");
    
    //         // Step 3: Crop using ImageMagick
    //         $cropCmd = "convert \"$imagePath\" -crop $geometry \"$croppedPath\"";
    //         exec($cropCmd . ' 2>&1', $cropOutput);
    
    //         Log::info('Cropping command executed.', [
    //             'annotation' => $annotation->name,
    //             'command' => $cropCmd,
    //             'output' => $cropOutput
    //         ]);
    
    //         if (!file_exists($croppedPath)) {
    //             Log::error('Cropped image not found.', ['path' => $croppedPath]);
    //             continue;
    //         }
    
    //         // Step 4: OCR using Tesseract
    //         $text = shell_exec("tesseract \"$croppedPath\" stdout");
    //         $text = trim(preg_replace('/\s+/', '', $text));
    //         Log::info("Tesseract output for [$name]: $text");
    
    //         if ($name === 'roll_no') {
    //             $rollNo = $text;
    //         } elseif ($name === 'department') {
    //             $dept = $text;
    //         }
    
    //         if (file_exists($croppedPath)) {
    //             unlink($croppedPath);
    //         }
    //     }
    
    //     // Step 5: Save submission
    //     $submission = Submission::create([
    //         'assignment_id' => $assignmentId,
    //         'file_path' => $path,
    //         'roll_no' => $rollNo,
    //         'dept' => $dept,
    //     ]);
    
    //     Log::info('Submission saved:', ['id' => $submission->id, 'roll_no' => $rollNo, 'dept' => $dept]);
    
    //     $course_id = session('last_opened_course');
    //     return redirect()->route('assignments.index', $course_id)->with('success', 'Submission uploaded successfully.');
    // }

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
}
