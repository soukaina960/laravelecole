<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Semestre;

class SemestreController extends Controller
{
    public function index() {
        return response()->json([
            'data' => Semestre::with('anneeScolaire')->get()
        ]);
}
};