<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // Enregistrer les présences
    public function markAttendance(Request $request, Course $course)
    {
        $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:present,absent,late,excused'
        ]);

        try {
            foreach ($request->attendances as $attendanceData) {
                Attendance::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'student_id' => $attendanceData['student_id'],
                        'date' => $request->date
                    ],
                    ['status' => $attendanceData['status']]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Présences enregistrées avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Récupérer les présences d'un cours
    public function getCourseAttendance(Course $course)
    {
        $attendances = Attendance::where('course_id', $course->id)
            ->with('student:id,name')
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy('date');

        return response()->json([
            'success' => true,
            'data' => $attendances
        ]);
    }
}