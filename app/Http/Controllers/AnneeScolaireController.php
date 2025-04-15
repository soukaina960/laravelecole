<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use Illuminate\Http\Request;

class AnneeScolaireController extends Controller
{public function index()
    {
        return AnneeScolaire::all();
    }

    public function semestres($anneeId)
    {
        $annee = AnneeScolaire::findOrFail($anneeId);
        return $annee->semestres;
    }
    
}
