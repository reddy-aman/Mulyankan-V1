<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Template;
use App\Models\Course;
use App\Models\Assignment_Annotation;
use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\SplitSubmission;
use Imagick;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class AssignmentController extends Controller
{
    public function index($courseNo)
    {
        $course = Course::where('id', $courseNo)->firstOrFail();
        $assignments = Assignment::where('course_number', $course->course_number)->get();
        session(['last_opened_course' => $courseNo]);

        return view('assignments.index', compact('assignments'));
    }

    public function create($course_id)
    {
        $course = Course::where('id', $course_id)->firstOrFail();
        return view('assignments.create', compact('course_id'));
    }

    public function storeTemplate(Request $request)
    {
        $request->validate([
            'template_pdf' => 'required|mimes:pdf|max:51200',
            'assignment_name' => 'required|string|max:255',
            'points' => 'nullable|numeric',
            'release_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'assignment_type' => 'required|string|in:Quiz,Bubble,Homework,Online,Programming',
        ]);

        $path = $request->file('template_pdf')->store('templates', 'public');
        $template = Template::create([
            'file_path' => $path,
        ]);

        $course_id = session('last_opened_course');
        $course = Course::where('id', $course_id)->firstOrFail();

        $type = $request->input('assignment_type');
        $release = $request->input('release_date');

        $fullPath = storage_path('app/public/' . $path);
        $cmd = "pdfinfo " . escapeshellarg($fullPath) . " | awk '/Pages:/ {print $2}'";
        $pageCount = intval(trim(shell_exec($cmd)));

        $assignment = Assignment::create([
            'Name' => $request->input('assignment_name'),
            'course_number' => $course->course_number,
            'points' => 0,
            'release_date' => $request->input('release_date'),
            'due_date' => $type === 'Quiz' ? $release : $request->input('due_date'),
            'status' => 'Edit Outline',
            'submissions_count' => 0,
            'template_id' => $template->id,
            'pages' => $pageCount,
            'type' => $type,
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
                    'page' => (int) $page,
                    'top' => $annotationData['top'],
                    'left' => $annotationData['left'],
                    'width' => $annotationData['width'],
                    'height' => $annotationData['height'],
                    'scale' => $globalScale,
                    'name' => $annotationData['name'],
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
        $assignment = Assignment::findOrFail($assignmentId);
        $template = Template::findOrFail($assignment->template_id);

        $annotations = Assignment_Annotation::where('assignment_id', $assignmentId)
            ->get()
            ->groupBy('page') // This is the correct field now
            ->map(function ($group, $page) {
                return $group->map(function ($annotation) {
                    return [
                        'top' => $annotation->top,
                        'left' => $annotation->left,
                        'width' => $annotation->width,
                        'height' => $annotation->height,
                        'name' => $annotation->name,
                    ];
                })->values();
            });


        return view('assignments.annotate-template', [
            'templateId' => $template->id,
            'filePath' => $template->file_path,
            'annotations' => $annotations, // must be passed as JSON-like
        ]);
    }

    public function uploadForm($assignmentId)
    {
        return view('assignments.upload-submission', compact('assignmentId'));
    }

    public function manageSubmission(int $assignmentId)
    {
        $assignment = Assignment::findOrFail($assignmentId);

        $submissions = Submission::where('assignment_id', $assignment->id)
            ->orderBy('created_at', 'asc')
            ->get();

        $allParts = [];

        foreach ($submissions as $submission) {
            $splits= SplitSubmission::where('submission_id', $submission->id)
                ->orderBy('id')
                ->get();

            $totalParts     = 0;
            $unassigned     = 0;
            $submissionParts = [];
            foreach ($splits as $idx => $split) {
                $relative = $split->file_path;
                $full = storage_path("app/$relative");
                $total = intval(trim(shell_exec(
                    "pdfinfo " . escapeshellarg($full) . " | awk '/Pages:/ {print \$2}'"
                )));

                $submissionParts[] = [
                    'part_number' => $idx + 1,
                    'file_path' => $relative,
                    'pages' => range(1, $total),
                    'roll_no'     => $split->roll_no,
                ];

                $totalParts++;
                if (empty($split->roll_no)) {
                    $unassigned++;
                }
            }

            $allParts[] = [
                'submission' => $submission,
                'parts' => $submissionParts,
                'totalParts'      => $totalParts,
                'unassignedCount' => $unassigned,
            ];
        }

        return view('assignments.manage-submission', [
            'assignment' => $assignment,
            'allSubmissions' => $allParts,
        ]);
    }


    public function destroySubmission(Assignment $assignment, Submission $submission)
    {
        $splitPaths = SplitSubmission::where('submission_id', $submission->id)
            ->pluck('file_path')   // e.g. ["private/submissions/.../folder/part1.pdf", ...]
            ->unique();            // in case there are multiple parts

        // 2) For each path, delete its parent directory
        foreach ($splitPaths as $relative) {
            // dirname("private/.../folder/part1.pdf") -> "private/.../folder"
            $fullDir = storage_path('app/' . dirname($relative));

            if (File::isDirectory($fullDir)) {
                File::deleteDirectory($fullDir);
            } else {
                Log::warning("Directory not found on disk", ['fullDir' => $fullDir]);
            }
        }

        // 3) Remove the DB records
        SplitSubmission::where('submission_id', $submission->id)->delete();
        $submission->delete();

        return redirect()
            ->route('assignments.manageSubmission', $assignment->id)
            ->with('success', 'Submission deleted successfully.');
    }

    // private function handleRotation(Request $request, string $fullPath, string $originalFilename): string
    // {
    //     if ($request->has('is_rotated') && $request->boolean('is_rotated')) {
    //         $rotatedDir = storage_path('app/temp_submissions');
    //         if (!file_exists($rotatedDir)) {
    //             mkdir($rotatedDir, 0755, true);
    //         }
    //         $rotatedPath = $rotatedDir . '/rotated_' . basename($originalFilename);

    //         $cmdRotate = "qpdf " . escapeshellarg($fullPath) . " --rotate=+180:1-z " . escapeshellarg($rotatedPath);
    //         exec($cmdRotate . ' 2>&1', $output, $returnVar);

    //         if ($returnVar !== 0) {
    //             abort(500, 'Failed to rotate PDF: ' . implode("\n", $output));
    //         }

    //         return $rotatedPath;
    //     }

    //     return $fullPath;
    // }

    private function splitAndStoreSubmissions(string $pdfPath, int $totalPages, int $allowedPages, Assignment $assignment, string $customFolderName, int $newSubmissionId)
    {
        // $annotation = Assignment_Annotation::where('assignment_id', $assignment->id)
        // ->where('name', 'Roll No')
        // ->first();

        // if (!$annotation) {
        //     throw new \Exception("No roll number annotation found.");
        // }

        $submissionParts = [];
        $assignmentName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $assignment->Name);
        $outputDir = storage_path('app/temp_submissions/' . $customFolderName . '/' . $assignmentName . '/');

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

            // $snippetFileName = 'snippet_' . basename($outputFileName, '.pdf') . '.png';


            // $this->extractRollNumberImage($outputFilePath, $annotation, $assignment, $customFolderName);

            $relativePath = "temp_submissions/{$customFolderName}/{$assignmentName}/{$outputFileName}";

            // 4) persist to DB!
            // SplitSubmission::create([
            //     'submission_id' => $newSubmissionId,
            //     'file_path'     => $relativePath,
            // ]);

            $submissionParts[] = [
                'file_path' => $relativePath,
                'start_page' => $startPage,         // page numbers we just processed
                'end_page' => $endPage,
            ];

            $startPage += $allowedPages;
            $part++;
        }

        return $submissionParts;
    }

    // private function extractRollNumberImage(string $pdfPath, Assignment_Annotation $annotation, Assignment $assignment, string $customFolderName)
    // {
    //     $assignmentName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $assignment->Name);
    //     $outputDir = storage_path('app/public/temp_snippets/' . $customFolderName . '/' . $assignmentName . '/');

    //     if (!file_exists($outputDir)) {
    //         mkdir($outputDir, 0755, true);
    //     }

    //     $outputFileName = 'snippet_' . basename($pdfPath, '.pdf') . '.png';
    //     $outputFilePath = $outputDir . $outputFileName;

    //     $dpi = 150;
    //     $scale = $annotation->scale;
    //     $top = $annotation->top * $scale;
    //     $left = $annotation->left * $dpi/72;
    //     $width = $annotation->width * $dpi/72;
    //     $height = $annotation->height * $dpi/72;

    //     $page = $annotation->page;

    //     $cmdRender = "pdftoppm -f {$page} -l {$page} -png -r {$dpi} "
    //         . escapeshellarg($pdfPath) . " " . escapeshellarg($outputDir . 'page_image');
    //     exec($cmdRender);

    //     $possiblePaths = glob($outputDir . 'page_image-*' . '.png');
    //     if (empty($possiblePaths)) {
    //         throw new \Exception("Failed to render PDF page to image.");
    //     }
    //     $renderedPagePath = $possiblePaths[0];

    //     if (!file_exists($renderedPagePath)) {
    //         throw new \Exception("Rendered page image not found.");
    //     }

    //     $cmdCrop = "convert "
    //         . escapeshellarg($renderedPagePath)
    //         . " -crop {$width}x{$height}+{$left}+{$top} +repage "
    //         . escapeshellarg($outputFilePath);

    //     exec($cmdCrop);

    //     @unlink($renderedPagePath);
    // }


    public function upload(Request $request, $assignmentId)
    {
        $request->validate([
            'submission_file' => 'required|file|max:20480', // max 10MB
        ]);

        $file = $request->file('submission_file');
        $path = $file->store('submissions');
        $fullPath = storage_path('app/private/' . $path);

        $submissionId = Submission::create([
            'assignment_id' => $assignmentId,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => 'submissions/' . basename($fullPath),
        ]);

        // $pdfPathToUse = $this->handleRotation($request, $fullPath, $path);

        $assignment = Assignment::findOrFail(id: $assignmentId);
        $allowedPages = $assignment->pages;

        $cmd = "pdfinfo " . escapeshellarg($fullPath) . " | awk '/Pages:/ {print $2}'";
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
            $submissionParts = $this->splitAndStoreSubmissions($fullPath, $totalPages, $allowedPages, $assignment, $customFolderName, $submissionId->id);
            // session(['submission_parts' => $submissionParts]);

            @unlink($fullPath);
            if (isset($pdfPathToUse) && file_exists($pdfPathToUse)) {
                @unlink($pdfPathToUse);
            }
        }

        return view('assignments.upload-submission', [
            'newSubmissionId' => $submissionId->id,
            'assignmentId' => $assignmentId,
            'customFolderName' => $customFolderName,
            'submissionParts' => $submissionParts,
            'success' => 'Submission split into parts. Please verify below.',
        ]);
        // return redirect()->route('assignments.index', session('last_opened_course'))->with('success', 'Roll numbers updated successfully.');

        // return redirect()
        // ->route('assignments.verifyRollNumbers', $assignmentId)
        // ->with('success', 'Submission uploaded and split successfully.');
    }


    // app/Http/Controllers/SubmissionController.php

    // public function viewPart(Request $request, string $path)
// {
//     $fullPath = storage_path("app/temp_submissions/{$path}");
//     if (!file_exists($fullPath)) {
//         abort(404, "PDF not found.");
//     }

    //     // if no ?page=… specified, default to 1
//     $page = (int) $request->query('page', 1);
//     if ($page < 1) {
//         abort(400, "Invalid page number.");
//     }

    //     // temp single‐page PDF
//     $tmp = sys_get_temp_dir() . '/pdf_page_' . uniqid() . '.pdf';
//     $cmd = sprintf(
//         'pdfseparate -f %1$d -l %1$d %2$s %3$s',
//         $page,
//         escapeshellarg($fullPath),
//         escapeshellarg($tmp)
//     );
//     exec($cmd, $_o, $code);
//     if ($code !== 0 || !file_exists($tmp)) {
//         abort(500, "Failed extracting page {$page}.");
//     }

    //     // stream inline
//     $response = response()->file(
//         $tmp,
//         [
//             'Content-Type'        => 'application/pdf',
//             'Content-Disposition' => 'inline; filename="'.basename($path, '.pdf')."_page{$page}.pdf\"",
//         ]
//     );

    //     register_shutdown_function(fn() => @unlink($tmp));
//     return $response;
// }

    public function thumbnail(Request $request)
    {
        // pull our PDF path from the query-string:
        $relative = $request->query('path');
        $page     = max(1, (int) $request->query('page', 1));
        $size     = $request->query('size', 'thumb');

        Log::info('Generating thumbnail', compact('relative','page','size'));

        $fullPdf = storage_path("app/{$relative}"); 
        if (! file_exists($fullPdf)) {
            abort(404, "PDF not found at {$fullPdf}");
        }

        $im = new \Imagick();
        $im->setResolution(300, 300);
        $im->readImage("{$fullPdf}[".($page-1)."]");
        $im->setImageFormat('png');

        if ($size !== 'full') {
            $im->scaleImage(200, 0);
        }

        return response($im->getImageBlob(), 200)
            ->header('Content-Type','image/png');
    }


    public function finalizeSubmission(Request $request)
    {
        $submissionId = $request->submission_id;
        $assignmentId = $request->assignment_id;
        $customFolder = $request->custom_folder;
        $rotationData = json_decode($request->input('rotations', '{}'), true);
        $deletedData = json_decode($request->input('deleted_pages', '{}'), true);
        $pageOrderData = json_decode($request->input('page_order', '{}'), true);

        $assignment = Assignment::findOrFail($assignmentId);
        $assignmentName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $assignment->Name);

        $submission = Submission::findOrFail($submissionId);
        $submissionfilename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $submission->file_name);
        $timestamp = date('Ymd_His');
        $folderName = "{$submissionfilename}_{$timestamp}";

        $tempDir = storage_path("app/temp_submissions/{$customFolder}/{$assignmentName}/");
        $finalDir = storage_path("app/private/submissions/{$customFolder}/{$assignmentName}/{$folderName}/");

        if (!file_exists($tempDir)) {
            Log::error('Temp directory not found', ['tempDir' => $tempDir]);
            return back()->withErrors(['Temporary files not found.']);
        }
        if (!file_exists($finalDir)) {
            mkdir($finalDir, 0755, true);
        }

        $partFiles = collect(glob($tempDir . 'part*.pdf'))->sort();

        foreach ($partFiles as $partPath) {
            $fileName = basename($partPath);
            $tempRelative = str_replace(storage_path('app/'), '', $partPath);
            $rotations = $rotationData[$tempRelative] ?? [];
            $toDelete = $deletedData[$tempRelative] ?? [];
            $reorder = $pageOrderData[$tempRelative] ?? [];

            if (!empty($rotations)) {
                // Build "+angle:page1,page2,..." e.g. "+180:1,6"
                $angle = intval(reset($rotations));
                $pages = implode(',', array_keys($rotations));
                $rotateParam = "+{$angle}:{$pages}";

                $cmd = sprintf(
                    "qpdf --replace-input --rotate=%s %s",
                    escapeshellarg($rotateParam),
                    escapeshellarg($partPath)
                );
                exec($cmd, $output, $exitCode);

                if ($exitCode !== 0) {
                    Log::error('Rotation failed', compact('partPath', 'rotateParam', 'exitCode', 'output'));
                }
            }

            if (!empty($reorder)) {
                $orderList = implode(',', array_map('intval', $reorder));

                $cmd = sprintf(
                    "qpdf --replace-input %s --pages . %s --",
                    escapeshellarg($partPath),
                    $orderList
                );
                exec($cmd, $outRe, $codeRe);
            }

            if (!empty($toDelete)) {
                $excludeFlags = array_map(function ($p) {
                    return 'x' . intval($p);
                }, $toDelete);
                $excludeList = implode(',', $excludeFlags); 

                $cmd = sprintf(
                    "qpdf --replace-input %s --pages . 1-z,%s --",
                    escapeshellarg($partPath),
                    $excludeList
                );

                exec($cmd, $outDel, $codeDel);

                if ($codeDel !== 0) {
                    Log::error('qpdf page‑deletion failed', compact('partPath', 'excludeList', 'codeDel', 'outDel'));
                }
            }

            $backup = $partPath . '.~qpdf-orig';
            if (file_exists($backup)) {
                @unlink($backup);
            }
            // Move into final storage
            $finalPath = $finalDir . $fileName;
            rename($partPath, $finalPath);

            SplitSubmission::create([
                'submission_id' => $submissionId,
                'file_path' => "private/submissions/{$customFolder}/{$assignmentName}/{$folderName}/{$fileName}",
            ]);
        }

        Storage::deleteDirectory("temp_submissions/{$customFolder}/{$assignmentName}");

        $assignment->status = 'Submission Uploaded';
        $assignment->save();

        return redirect()
            ->route('assignments.index', session('last_opened_course'))
            ->with('success', 'Final submission saved successfully.');
    }


    public function verifyRollNumbers(Assignment $assignment, Submission $submission)
    {
        $course = Course::where('course_number', $assignment->course_number)->firstOrFail();
        $annotation = Assignment_Annotation::where('assignment_id', $assignment->id)
            ->where('name', 'Roll No')
            ->firstOrFail();

        $entries = SplitSubmission::where('submission_id', $submission->id)
            ->get(['id','roll_no','file_path']);
        
        $submissionParts = $entries->map(function($split, $idx) use($annotation) {
                // build a safe query‐string URL
                $snippetUrl = route('submissions.cropSnippet')
                   . '?path='   . urlencode($split->file_path)
                   . '&page=1'
                   . '&top='    . $annotation->top
                   . '&left='   . $annotation->left
                   . '&width='  . $annotation->width
                   . '&height=' . $annotation->height;
            
                return (object)[
                    'index'       => $idx,
                    'split_id'    => $split->id,
                    'roll_no'     => $split->roll_no,
                    'file_path'   => $split->file_path,
                    'snippetPath'=> $snippetUrl,
                ];
        })->all();

        $students = Student::where('course_id', $course->id)
            ->select('id', 'sid', 'name', 'email')
            ->get();

        return view('assignments.verify_roll_numbers', [
            'assignment' => $assignment,
            'students' => $students,    
            'submissionParts' => $submissionParts,
            'course_id' => $course->id,
        ]);
    }

    public function cropSnippet(Request $request)
    {
        $path = $request->query('path');
        $page = max(1, intval($request->query('page', 1))) - 1;
        $top = floatval($request->query('top'));
        $left = floatval($request->query('left'));
        $width = floatval($request->query('width'));
        $height = floatval($request->query('height'));

        $fullPdf = storage_path("app/{$path}");

        if (!file_exists($fullPdf)) {
            abort(404, "PDF not found.");
        }

        $imagick = new Imagick();
        $imagick->setResolution(300, 300);
        $imagick->readImage("{$fullPdf}[{$page}]");
        $imagick->setImageFormat('png');

        $scale = 300 / 72;

        $cropWidth = (int) round($width * $scale);
        $cropHeight = (int) round($height * $scale);
        $cropLeft = (int) round($left * $scale);
        $cropTop = (int) round($top * $scale);
    
        $imagick->cropImage($cropWidth, $cropHeight, $cropLeft, $cropTop);

        return response($imagick->getImageBlob(), 200)
            ->header('Content-Type', 'image/png');
    }


    public function storeRollNumbers(Request $request, Assignment $assignment)
    {
        $data = $request->validate([
            'parts'               => 'required|array',
            'parts.*.split_id'    => 'required|integer|exists:split_submissions,id',
            'roll_numbers'        => 'required|array',
        ]);
    
        foreach ($data['parts'] as $i => $part) {
            SplitSubmission::where('id', $part['split_id'])
                ->update(['roll_no' => $data['roll_numbers'][$i] ?? null]);
        }
    
        return redirect()
            ->route('assignments.index', session('last_opened_course'))
            ->with('success', 'Roll numbers saved.');
    }
    

    public function edit($id)
    {
        $assignment = Assignment::findOrFail($id);
        return view('assignments.edit', compact('assignment'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Name' => 'required|string|max:255',
            'release_date' => 'nullable|date',
        ]);

        $assignment = Assignment::findOrFail($id);
        $assignment->Name = $request->input('Name');
        $assignment->release_date = $request->input('release_date');
        $assignment->due_date = $request->input('release_date');
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
