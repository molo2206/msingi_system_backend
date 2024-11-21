<?php

namespace App\Http\Controllers;

use App\Models\SecteurActivity;
use Illuminate\Http\Request;

class SecteurActivityController extends Controller
{
    public function index()
    {
        return response()->json([
            'code' => 200,
            'message' => 'List of secteur activity',
            'data' => SecteurActivity::orderBy('name', 'ASC')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:secteur_activities',
        ]);
        SecteurActivity::create($request->all());
        return response()->json([
            'code' => 200,
            'message' => 'Sécteur d\'activité créer succès!',
            'data' => SecteurActivity::where('status', 1)
                ->where('deleted', 0)->orderBy('name', 'ASC')->get()
        ]);
    }

    public function update(Request $request, $id)
    {
        $secteur = SecteurActivity::find($id);
        if (!$secteur) {
            return response()->json([
                'code' => 404,
                'message' => 'Secteur not found',
            ]);
        }
        if ($secteur->name !== $request->name) {
            $request->validate([
                'name' => 'required|unique:secteur_activities'
            ]);
            $secteur->name = $request->name;
        }
        $secteur->update($request->all());
        return response()->json([
            'code' => 200,
            'message' => 'Fonction updated successfully',
            'data' => SecteurActivity::where('status', 1)
                ->where('deleted', 0)->orderBy('name', 'ASC')->get()
        ]);
    }
}
