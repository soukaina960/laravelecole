<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use Illuminate\Http\Request;

class AnneeScolaireController extends Controller
{public function index() {
    return response()->json([
        'data' => AnneeScolaire::all()
    ]);
}

    
}
