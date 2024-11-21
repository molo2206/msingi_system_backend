<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use App\Models\Fonction;
use Illuminate\Http\Request;

class FonctionController extends Controller
{
    public function index() {}
    public function show($id)
    {
        if (!Departement::find($id)) {
            return response()->json([
                'code' => 404,
                'message' => 'Departement not found',
            ]);
        }
        return response()->json([
            'code' => 200,
            'message' => 'List of departments',
            'data' => Fonction::where('departement_id', $id)->orderBy('name', 'ASC')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:fonctions',
            'departement_id' => 'required'
        ]);
        $fonction = Fonction::create($request->all());
        return response()->json([
            'code' => 201,
            'message' => 'Fonction created successfully',
            'data' => Fonction::with('departement')->where('status', 1)
                ->where('deleted', 0)->orderBy('name', 'ASC')->get()
        ]);
    }

    public function update(Request $request, $id)
    {
        $fonction = Fonction::find($id);
        if(!$fonction){
             return response()->json([
                'code' => 404,
                'message' => 'Fonction not found',
            ]);
        }
        if ($fonction->name !== $request->name) {
            $request->validate([
                'name' => 'required|unique:fonctions'
            ]);
            $fonction->name = $request->name;
        }

        if ($fonction->departement_id !== $request->departement_id) {
            $request->validate([
                'departement_id' => 'required'
            ]);
            $fonction->departement_id = $request->departement_id;
        }
        $fonction->update($request->all());
        return response()->json([
            'code' => 200,
            'message' => 'Fonction updated successfully',
            'data' => $fonction->with('departement')->where('status', 1)
                ->where('deleted', 0)->orderBy('name', 'ASC')->first()
        ]);
    }
}
