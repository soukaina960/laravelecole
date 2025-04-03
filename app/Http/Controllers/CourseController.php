<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['professor', 'classroom'])
                       ->orderBy('created_at', 'desc')
                       ->get();
    
        return response()->json([
            'success' => true,
            'data' => $courses
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
           
        ]);

        try {
            $course = Course::create($validated);
            return response()->json([
                'success' => true,
                'data' => $course,
                'message' => 'Course created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create course',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Course $course)
    {
        return response()->json([
            'success' => true,
            'data' => $course->load(['professor', 'classroom'])
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'professor_id' => 'sometimes|integer|exists:professeurs,id',
            'classroom_id' => 'sometimes|integer|exists:classrooms,id'
        ]);

        try {
            $course->update($validated);
            return response()->json([
                'success' => true,
                'data' => $course,
                'message' => 'Course updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update course',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Course $course)
    {
        try {
            $course->delete();
            return response()->json([
                'success' => true,
                'message' => 'Course deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete course',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}