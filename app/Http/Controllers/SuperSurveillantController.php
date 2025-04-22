<?php

namespace App\Http\Controllers;

use App\Models\SuperSurveillant;
use Illuminate\Http\Request;

class SuperSurveillantController extends Controller
{
    public function index()
    {
        $supers = SuperSurveillant::where('role', 'super_surveillant')->get();
        return view('super_surveillants.index', compact('supers'));
    }

    public function create()
    {
        return view('super_surveillants.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:utilisateurs',
            'mot_de_passe' => 'required|string|min:6',
        ]);

        $data['role'] = 'super_surveillant';
        $data['mot_de_passe'] = bcrypt($data['mot_de_passe']);

        SuperSurveillant::create($data);

        return redirect()->route('super_surveillants.index')->with('success', 'Super Surveillant ajouté !');
    }
}
