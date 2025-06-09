<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Template;
use App\Models\Course;
use App\Models\Assignment_Annotation;
use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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

        $fullPath = storage_path('app/public/' . $path);
        $cmd = "pdfinfo " . escapeshellarg($fullPath) . " | awk '/Pages:/ {print $2}'";
        $pageCount = intval(trim(shell_exec($cmd)));

        $assignment = Assignment::create([
            'Name'              => $request->input('assignment_name'),
            'course_number'     => $course->course_number,
            'points'            => 0,
            'release_date'      => $request->input('release_date'),
            'due_date'          => $type === 'Quiz' ? $release : $request->input('due_date'),
            'status'            => 'Edit Outline',
            'submissions_count' => 0,
            'template_id'       => $template->id,
            'pages'             => $pageCount,
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

        $globalScale = isset($data['scale']) ? $data['scale'] : 1;
        $annotationsData = isset($data['annotations']) ? $data['annotations'] : [];

        $assignment_id = session('current_assignment_id');
        Assignment_Annotation::where('assignment_id', $assignment_id)->delete();

        foreach ($annotationsData as $page => $annotations) {
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
                    'scale'=> $globalScale,
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
        session(['current_assignment_id' => $assignmentId]);
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

    private function handleRotation(Request $request, string $fullPath, string $originalFilename): string
    {
        if ($request->has('is_rotated') && $request->boolean('is_rotated')) {
            $rotatedDir = storage_path('app/temp_submissions');
            if (!file_exists($rotatedDir)) {
                mkdir($rotatedDir, 0755, true);
            }
            $rotatedPath = $rotatedDir . '/rotated_' . basename($originalFilename);
    
            $cmdRotate = "qpdf " . escapeshellarg($fullPath) . " --rotate=+180:1-z " . escapeshellarg($rotatedPath);
            exec($cmdRotate . ' 2>&1', $output, $returnVar);
    
            if ($returnVar !== 0) {
                abort(500, 'Failed to rotate PDF: ' . implode("\n", $output));
            }
    
            return $rotatedPath;
        }
    
        return $fullPath;
    }

    private function splitAndStoreSubmissions(string $pdfPath, int $totalPages, int $allowedPages, Assignment $assignment, string $customFolderName)
    {
        $annotation = Assignment_Annotation::where('assignment_id', $assignment->id)
        ->where('name', 'Roll No')
        ->first();

        if (!$annotation) {
            throw new \Exception("No roll number annotation found.");
        }

        $submissionParts = [];
        $assignmentName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $assignment->Name);
        $outputDir = storage_path('app/private/submissions/' . $customFolderName . '/' . $assignmentName . '/');
    
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
    
        $startPage = 1;
        $part = 1;
    
        while ($startPage <= $totalPages) {
            $endPage = min($startPage + $allowedPages - 1, $totalPages);
            $outputFileName = 'part' . $part . '.pdf';
            $outputFilePath = $outputDir . $outputFileName;
    
            $splitPagesPattern = $outputDir . "page_%d.pdf";
    
            // Separate pages
            $cmdSeparate = "pdfseparate -f $startPage -l $endPage " . escapeshellarg($pdfPath) . " $splitPagesPattern";
            exec($cmdSeparate);
    
            // Combine them back into one chunk
            $pageFiles = [];
            for ($i = $startPage; $i <= $endPage; $i++) {
                $pageFiles[] = $outputDir . "page_$i.pdf";
            }
            $pageFilesStr = implode(' ', array_map('escapeshellarg', $pageFiles));
            $cmdUnite = "pdfunite $pageFilesStr " . escapeshellarg($outputFilePath);
            exec($cmdUnite);
    
            // Clean up single-page files
            foreach ($pageFiles as $pageFile) {
                @unlink($pageFile);
            }
    
            // $submission = Submission::create([
            //     'assignment_id' => $assignment->id,
            //     'file_path' => 'submissions/' . $customFolderName . '/' . $assignmentName . '/' . $outputFileName,
            // ]);
            $snippetFileName = 'snippet_' . basename($outputFileName, '.pdf') . '.png';

    
            $this->extractRollNumberImage($outputFilePath, $annotation, $assignment, $customFolderName);
            // $submissionParts[] = [
            //     'submission_id' => $submission->id,
            //     'snippetFileName' => $snippetFileName,
            // ];
            
            $submissionParts[] = [
                // we’ll assign the real submission_id later
                'file_path'       => 'submissions/' . $customFolderName . '/' . $assignmentName . '/' . $outputFileName,
                'snippetFileName' => $snippetFileName,
            ];

            $startPage += $allowedPages;
            $part++;
        }

        return $submissionParts;
    }

    private function extractRollNumberImage(string $pdfPath, Assignment_Annotation $annotation, Assignment $assignment, string $customFolderName)
    {
        $assignmentName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $assignment->Name);
        $outputDir = storage_path('app/public/temp_snippets/' . $customFolderName . '/' . $assignmentName . '/');

        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $outputFileName = 'snippet_' . basename($pdfPath, '.pdf') . '.png';
        $outputFilePath = $outputDir . $outputFileName;
    
        $dpi = 150;
        $scale = $annotation->scale;
        $top = $annotation->top * $scale;
        $left = $annotation->left * $dpi/72;
        $width = $annotation->width * $dpi/72;
        $height = $annotation->height * $dpi/72;
    
        $page = $annotation->page;
    
        $cmdRender = "pdftoppm -f {$page} -l {$page} -png -r {$dpi} "
            . escapeshellarg($pdfPath) . " " . escapeshellarg($outputDir . 'page_image');
        exec($cmdRender);
    
        $possiblePaths = glob($outputDir . 'page_image-*' . '.png');
        if (empty($possiblePaths)) {
            throw new \Exception("Failed to render PDF page to image.");
        }
        $renderedPagePath = $possiblePaths[0];
        
        if (!file_exists($renderedPagePath)) {
            throw new \Exception("Rendered page image not found.");
        }

        $cmdCrop = "convert "
            . escapeshellarg($renderedPagePath)
            . " -crop {$width}x{$height}+{$left}+{$top} +repage "
            . escapeshellarg($outputFilePath);
    
        exec($cmdCrop);
    
        @unlink($renderedPagePath);
    }
    
    
    public function upload(Request $request, $assignmentId)
    {
        $request->validate([
            'submission_file' => 'required|file|max:20480', // max 10MB
        ]);
    
        $path = $request->file('submission_file')->store('submissions');        
        $fullPath = storage_path('app/private/' . $path);

        $pdfPathToUse = $this->handleRotation($request, $fullPath, $path);

        $assignment = Assignment::findOrFail(id: $assignmentId);
        $allowedPages = $assignment->pages;

        $cmd = "pdfinfo " . escapeshellarg($pdfPathToUse) . " | awk '/Pages:/ {print $2}'";
        $totalPages = intval(trim(shell_exec($cmd)));

        $course = Course::where('course_number', $assignment->course_number)->first();
        if (!$course) {
            return back()->with('error', 'Course not found.');
        }
    
        $customFolderName = $assignment->course_number . '_' . $course->term . '_' . $course->year;
        $customFolderName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $customFolderName); 


        if ($totalPages <= $allowedPages) {
            Submission::create([
                'assignment_id' => $assignmentId,
                'file_path' => 'submissions/' . basename($path),
            ]);
        } else {
            $submissionParts = $this->splitAndStoreSubmissions($pdfPathToUse, $totalPages, $allowedPages, $assignment, $customFolderName);
            session(['submission_parts' => $submissionParts]);

            @unlink($fullPath);
            if (isset($pdfPathToUse) && file_exists($pdfPathToUse)) {
                @unlink($pdfPathToUse);
            }
        }
    
        $assignment->status = 'Submission Uploaded';
        $assignment->save();
    
        return redirect()
        ->route('assignments.verifyRollNumbers', $assignmentId)
        ->with('success', 'Submission uploaded and split successfully.');
    }
    
    // public function verifyRollNumbers($assignmentId)
    // {
    //     $submissionPartsMapping = session('submission_parts', []);

    //     $assignment = Assignment::findOrFail($assignmentId);
    //     $course_id = session('last_opened_course');
    
    //     $students = Student::where('course_id', $course_id)
    //     ->select('id', 'sid', 'name', 'email')
    //     ->get();    
        
    //     $files = Storage::disk('public')->files('temp_snippets'); // assuming under public disk

    //     $submissionParts = [];
    
    //     foreach ($files as $file) {
    //         $filename = basename($file);
        
    //         // Try to match snippet file with submission ID
    //         $submissionId = null;
    //         foreach ($submissionPartsMapping as $part) {
    //             if ($part['snippetFileName'] === $filename) {
    //                 $submissionId = $part['submission_id'];
    //                 break;
    //             }
    //         }
        
    //         $submissionParts[] = [
    //             'snippetPath' => asset('storage/temp_snippets/' . $filename),
    //             'filename' => $filename,
    //             'submission' => (object) [
    //                 'id' => $submissionId ?? pathinfo($filename, PATHINFO_FILENAME)
    //             ]
    //         ];
    //     }

    //     return view('assignments.verify_roll_numbers', [
    //         'assignment' => $assignment,
    //         'students' => $students,
    //         'course_id' => $course_id,
    //         'submissionParts' => $submissionParts,
    //     ]);
    // }
    
    public function verifyRollNumbers($assignmentId)
    {
        $assignment = Assignment::findOrFail($assignmentId);

        $course = Course::where('course_number', $assignment->course_number)
                        ->firstOrFail();
    
        // Build "CourseCode_Term_Year"
        $baseFolder = preg_replace(
            '/[^A-Za-z0-9_\-]/',
            '_',
            "{$assignment->course_number}_{$course->term}_{$course->year}"
        );
    
        // Sanitize assignment name
        $assignmentName = preg_replace(
            '/[^A-Za-z0-9_\-]/',
            '_',
            $assignment->Name
        );
    
        // Full snippet folder under storage/app/public/temp_snippets/
        $customFolderName = "{$baseFolder}/{$assignmentName}";
        
        // 1) Grab the pending parts from session
        $pending = session('submission_parts', []);
    
        // 2) Fetch assignment & students as before
        $assignment = Assignment::findOrFail($assignmentId);
        $course_id  = session('last_opened_course');
    
        $students = Student::where('course_id', $course_id)
                           ->select('id','sid','name','email')
                           ->get();
    
        // 3) Map each pending part => a structure with an index
        $submissionParts = collect($pending)
            ->map(function($part, $idx) use ($customFolderName) {
                return (object)[
                    // index used for form input names
                    'index'       => $idx,
                    // URL to display the roll-no snippet
                    'snippetPath' => asset('storage/temp_snippets/' . $customFolderName . '/' .$part['snippetFileName']),
                    // original PDF‐chunk path to save later
                    'file_path'   => $part['file_path'],
                ];
            })
            ->toArray();
    
        // 4) Return the view—no DB writes here
        return view('assignments.verify_roll_numbers', [
            'assignment'      => $assignment,
            'students'        => $students,
            'submissionParts' => $submissionParts,
            'course_id'       => $course_id,
        ]);
    }
    
    public function storeRollNumbers(Request $request, $assignmentId)
    {
        $request->validate([
            'roll_numbers' => 'required|array',
        ]);
    
        $pending = session('submission_parts', []);

        foreach ($pending as $idx => $part) {
            Submission::create([
                'assignment_id' => $assignmentId,
                'file_path'     => $part['file_path'],
                'roll_no'       => $request->roll_numbers[$idx],
            ]);
        }
        // foreach ($request->roll_numbers as $submissionId => $rollNumber) {
        //     $submission = Submission::findOrFail($submissionId);
        //     $submission->roll_no = $rollNumber;
        //     $submission->save();
        // }
        session()->forget('submission_parts');
        return redirect()->route('assignments.index', session('last_opened_course'))->with('success', 'Roll numbers updated successfully.');
    }
    
    public function edit($id)
    {
        $assignment = Assignment::findOrFail($id);
        return view('assignments.edit', compact('assignment'));
    }

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
