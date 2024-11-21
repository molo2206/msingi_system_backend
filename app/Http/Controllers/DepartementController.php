<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use Illuminate\Http\Request;

class DepartementController extends Controller
{
    public function index()
    {
        return response()->json([
            'code' => 200,
            'message' => 'List of departments',
            'data' => Departement::with('fonctions')->orderBy('name', 'ASC')->get()
        ]);
    }

    public function show($id) {}

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:departements'
        ]);
        $departement = Departement::create($request->all());
        return response()->json([
            'code' => 201,
            'message' => 'Departement created successfully',
            'data' => $departement->with('fonctions')->where('status', 1)
                ->where('deleted', 0)->orderBy('name', 'ASC')->get()
        ]);
    }

    public function update(Request $request, $id)
    {
        $departement = Departement::find($id);
        if ($departement->name !== $request->name) {
            $request->validate([
                'name' => 'required|unique:companies'
            ]);
            $departement->name = $request->name;
        }
        $departement->update($request->all());
        return response()->json([
            'code' => 200,
            'message' => 'Departement updated successfully',
            'data' => $departement->with('fonctions')->where('status', 1)
            ->where('deleted', 0)->orderBy('name', 'ASC')->get()
        ]);
    }
}
