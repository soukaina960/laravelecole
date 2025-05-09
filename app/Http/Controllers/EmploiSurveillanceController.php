<!-- ?php

// namespace App\Http\Controllers;

// use App\Models\EmploiSurveillance;
// use Illuminate\Http\Request;

// class EmploiSurveillanceController extends Controller
// {
//     public function index()
//     {
//         return response()->json(EmploiSurveillance::all());
//     }

//     public function store(Request $request)
//     {
//         $request->validate([
//             'jour' => 'required|string',
//             'heure_debut' => 'required',
//             'heure_fin' => 'required',
//             'surveillant_id' => 'required|exists:utilisateurs,id',
//         ]);

//         $emploi = EmploiSurveillance::create($request->all());

//         return response()->json($emploi, 201);
//     }

//     public function show($id)
//     {
//         return response()->json(EmploiSurveillance::findOrFail($id));
//     }

//     public function update(Request $request, $id)
//     {
//         $emploi = EmploiSurveillance::findOrFail($id);
//         $emploi->update($request->all());

//         return response()->json($emploi);
//     }

//     public function destroy($id)
//     {
//         EmploiSurveillance::destroy($id);
//         return response()->json(['message' => 'Emploi supprimé']);
//     }
//     // En bas de ton controller
// public function getBySurveillant($surveillant_id)
// {
//     $emplois = EmploiSurveillance::where('surveillant_id', $surveillant_id)->get();

//     return response()->json($emplois);
// }

} -->
