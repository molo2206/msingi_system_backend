<?php

namespace App\Http\Controllers;

use App\Models\Ressources;
use Illuminate\Http\Request;

class RessourcesController extends Controller
{
    public function index()
    {
        //All modules
        $ressource = Ressources::with('module')->where('deleted', 0)->get();
        return response()->json([
            'code' => 200,
            'message' => 'Ressource retrieved successfully',
            'data' => $ressource
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:ressources',
            'module_id' => 'required',
            'label' => 'required',
            'fonctionnalite' => 'required',
        ]);

        $ressource = Ressources::create($request->all());
        return response()->json([
            'code' => 200,
            'message' => 'Ressource created successfully',
            'data' => $ressource->with('module')->where('status', 1)
                ->where('deleted', 0)->orderBy('name', 'ASC')->get()
        ],200);
    }

    public function update(Request $request, $id)
    {
        $ressource = Ressources::find($id);
        if ($ressource->name !== $request->name) {
            $request->validate([
                'name' => 'required|unique:ressources'
            ]);
            $ressource->name = $request->name;
        }
        $ressource->update($request->all());
        return response()->json([
            'code' => 200,
            'message' => 'Ressource updated successfully',
            'data' => $ressource->with('module')->where('status', 1)
                ->where('deleted', 0)->orderBy('name', 'ASC')->get()
        ]);
    }

    public function destroy($id)
    {
        $ressource = Ressources::find($id);
        if ($ressource) {
            $ressource->deleted = 1;
            $ressource->save();
        }
        return response()->json([
            'code' => 200,
            'message' => 'Module deleted successfully',
            'data' => $ressource->with('module')->where('status', 1)
                ->where('deleted', 0)->orderBy('name', 'ASC')->get()
        ]);
    }
    public function status(Request $request, $id)
    {
        $request->validate([
            'status' => 'required',
        ]);
        $ressource = Ressources::find($id);
        if ($ressource) {
            $ressource->status = $request->status;
            $ressource->save();
        }
        return response()->json([
            'code' => 200,
            'message' => 'Status updated!',
            'data' => $ressource->with('module')->where('status', 1)
                ->where('deleted', 0)->orderBy('name', 'ASC')->get()
        ]);
    }
}
