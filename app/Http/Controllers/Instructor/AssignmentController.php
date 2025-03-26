<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Template;
use App\Models\Course;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index($courseNo)
    {
        // Retrieve the course by course_no or similar field
        $course = Course::where('id', $courseNo)->firstOrFail();
        // $course = Course::where('course_no', $courseNo)->firstOrFail();

        // Fetch assignments for that course
        $assignments = Assignment::where('course_id', $course->course_number)->get();

        // Check role (example: an instructor can do everything, 
        // a student can only view). Adjust logic as needed.
        $user = auth()->user();
        $isInstructor = $user && $user->role === 'instructor';

        return view('assignments.index', compact('course', 'assignments', 'isInstructor'));
    }

    public function create($course_id)
    {
        // $this->authorizeInstructor(); // Ensure only instructor

        $course = Course::where('id', $course_id)->firstOrFail();
        return view('assignments.create', compact('course_id'));
    }

    public function showUploadForm()
    {
        return view('assignments.upload-template');
    }

    /**
     * Handle the uploaded template PDF, store it, and redirect to annotation.
     */
    public function storeTemplate(Request $request)
    {
        $request->validate([
            'template_pdf' => 'required|mimes:pdf|max:51200', // up to 50MB
        ]);

        // Store the PDF in public/templates
        $path = $request->file('template_pdf')->store('templates', 'public');

        // Optionally create a DB record for the template
        $template = Template::create([
            'file_path' => $path,
            // annotation_data will be filled after annotation
            'annotation_data' => null,
        ]);

        return redirect()->route('assignments.annotateTemplate', [
            'template_id' => $template->id,
        ])->with('success', 'Template uploaded! Please annotate all pages.');
    }

    /**
     * Show the annotation page with page navigation, multiple boxes per page, etc.
     */
    public function annotateTemplate(Request $request)
    {
        $templateId = $request->query('template_id');
        $template = Template::findOrFail($templateId);

        // Pass the file path to the view
        return view('assignments.annotate-template', [
            'templateId' => $template->id,
            'filePath' => $template->file_path,
        ]);
    }

    /**
     * Save the multi-page annotation data to the DB.
     */
    public function saveAnnotation(Request $request)
    {
        $request->validate([
            'template_id'    => 'required|integer',
            'annotation_data' => 'required|json', // multi-page coords
        ]);

        $template = Template::findOrFail($request->input('template_id'));

        // Store the JSON data
        $template->annotation_data = $request->input('annotation_data');
        $template->save();

        return redirect()->back()->with('success', 'Annotation saved successfully!');
    }

    /**
     * Example: Split a student's submission using the stored annotations.
     * This approach converts each PDF page to an image, then crops the regions.
     */
    public function splitSubmission(Request $request)
    {
        $request->validate([
            'student_pdf'  => 'required|mimes:pdf|max:51200',
            'template_id'  => 'required|integer',
        ]);

        $template = Template::findOrFail($request->input('template_id'));
        if (!$template->annotation_data) {
            return back()->withErrors('No annotations found for this template.');
        }

        $annotationData = json_decode($template->annotation_data, true);
        // Store the student's submission
        $studentPdfPath = $request->file('student_pdf')->store('submissions', 'public');

        // Convert each page to an image (using Poppler's pdftoppm or similar)
        // Then crop each region based on $annotationData
        // This is pseudo-code; adapt to your environment

        // 1. Convert all pages to images
        // Example command: pdftoppm -png /full/path/to/student.pdf /output/path/page
        $fullStudentPdfPath = storage_path("app/public/{$studentPdfPath}");
        $outputDir = storage_path("app/public/submissions/images_" . uniqid());
        mkdir($outputDir);

        $command = "pdftoppm -png " . escapeshellarg($fullStudentPdfPath) . " " . escapeshellarg($outputDir . '/page');
        exec($command, $output, $returnCode);
        if ($returnCode !== 0) {
            return back()->withErrors('Error converting PDF to images.');
        }

        // Suppose each page is now page-1.png, page-2.png, etc.
        // $annotationData is structured like:
        // {
        //   "1": {
        //       "roll_region": { "top":..., "left":..., "width":..., "height":... },
        //       "dept_region": { ... },
        //       "questions": [ { "name":"Q1", "top":..., "left":..., "width":..., "height":... }, ... ]
        //   },
        //   "2": { ... },
        //   ...
        // }

        // 2. For each page in annotationData, crop out each region
        foreach ($annotationData as $pageNumber => $pageData) {
            // pageNumber is 1-based
            $imagePath = "{$outputDir}/page-{$pageNumber}.png";
            if (!file_exists($imagePath)) {
                continue;
            }

            // For example, crop the roll_region
            if (!empty($pageData['roll_region'])) {
                $coords = $pageData['roll_region'];
                $this->cropImage($imagePath, $coords, "roll_region_page_{$pageNumber}.png");
            }
            // Crop dept_region
            if (!empty($pageData['dept_region'])) {
                $coords = $pageData['dept_region'];
                $this->cropImage($imagePath, $coords, "dept_region_page_{$pageNumber}.png");
            }
            // Crop each question
            if (!empty($pageData['questions'])) {
                foreach ($pageData['questions'] as $qIndex => $qCoords) {
                    $this->cropImage($imagePath, $qCoords, "question_{$qIndex}_page_{$pageNumber}.png");
                }
            }
        }

        return back()->with('success', 'Submission split successfully!');
    }

    /**
     * Example helper function to crop an image using ImageMagick's `convert` or `magick` command.
     */
    private function cropImage($imagePath, $coords, $outputFileName)
    {
        $top = (int) $coords['top'];
        $left = (int) $coords['left'];
        $width = (int) $coords['width'];
        $height = (int) $coords['height'];

        // Where to save the cropped image
        $outputDir = dirname($imagePath);
        $outputPath = $outputDir . '/' . $outputFileName;

        // Crop command: convert input.png -crop WxH+X+Y output.png
        // e.g., convert image.png -crop 150x50+50+100 cropped.png
        $command = "convert " . escapeshellarg($imagePath)
            . " -crop {$width}x{$height}+{$left}+{$top} "
            . escapeshellarg($outputPath);
        exec($command, $out, $returnCode);
    }
    
    /**
     * Simple helper to ensure only an instructor can proceed.
     */
    private function authorizeInstructor()
    {
        if (!auth()->check() || auth()->user()->role !== 'instructor') {
            abort(403, 'Unauthorized action.');
        }
    }
}
