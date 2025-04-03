<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssignmentController extends Controller
{
    /**
     * Récupère tous les devoirs/examens
     */
    public function index()
    {
        try {
            $assignments = Assignment::with(['course', 'submissions'])
                ->orderBy('due_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $assignments,
                'message' => 'Assignments retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve assignments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crée un nouveau devoir/examen
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'course_id' => 'required|exists:courses,id',
            'type' => 'required|in:assignment,exam'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        try {
            $assignment = Assignment::create($request->all());

            return response()->json([
                'success' => true,
                'data' => $assignment,
                'message' => 'Assignment created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create assignment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les soumissions pour un devoir
     */
    public function getSubmissions(Assignment $assignment)
    {
        try {
            $submissions = $assignment->submissions()
                ->with(['student.user'])
                ->get();

            return response()->json([
                'success' => true,
                'data' => $submissions,
                'message' => 'Submissions retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve submissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enregistre ou met à jour une note
     */
    public function submitGrade(Assignment $assignment, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'grade' => 'required|numeric|min:0|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        try {
            $submission = AssignmentSubmission::updateOrCreate(
                [
                    'assignment_id' => $assignment->id,
                    'student_id' => $request->student_id
                ],
                [
                    'grade' => $request->grade,
                    'submitted_at' => now()
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $submission,
                'message' => 'Grade submitted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit grade',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}