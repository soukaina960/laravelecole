<?php

namespace App\Http\Controllers;

use App\Models\Etudiant; // Import the Student model
use App\Models\Absence; // Import the Absence model
use App\Models\Incident; // Import the Incident model
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function getStatistics()
    {
        // Fetching statistics using models, not controllers
        $studentsCount = Etudiant::count(); // Count the number of students
        $absencesCount = Absence::count(); // Count the number of absences
        $justifiedAbsencesCount = Absence::where('justifiee', true)->count(); // Count justified absences
        $unjustifiedAbsencesCount = Absence::where('justifiee', false)->count(); // Count unjustified absences
        $incidentsCount = Incident::count(); // Count the number of incidents

        // Return statistics as JSON
        return response()->json([
            'studentsCount' => $studentsCount,
            'absencesCount' => $absencesCount,
            'justifiedAbsencesCount' => $justifiedAbsencesCount,
            'unjustifiedAbsencesCount' => $unjustifiedAbsencesCount,
            'incidentsCount' => $incidentsCount,
        ]);
    }
}
