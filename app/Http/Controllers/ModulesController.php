<?php

namespace App\Http\Controllers;

use App\Models\Modules;
use Illuminate\Http\Request;

class ModulesController extends Controller
{
    public function index()
    {
        //All modules
        $module = Modules::with('ressource')->where('status', 1)->where('deleted', 0)->get();
        return response()->json([
            'code' => 200,
            'message' => 'Modules retrieved successfully',
            'data' => $module
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:modules',
            'label' => 'required',
            'fonctionnalite' => 'required'
        ]);
        $module = Modules::create($request->all());
        return response()->json([
            'code' => 201,
            'message' => 'Modules created successfully',
            'data' => $module->with('ressource')->where('status', 1)
                ->where('deleted', 0)->orderBy('name', 'ASC')->get()
        ]);
    }

    public function update(Request $request, $id)
    {
        $module = Modules::find($id);
        if ($module->name !== $request->name) {
            $request->validate([
                'name' => 'required|unique:modules'
            ]);
            $module->name = $request->name;
        }
        $module->update($request->all());
        return response()->json([
            'code' => 200,
            'message' => 'Module updated successfully',
            'data' => $module->with('ressource')->where('status', 1)
                ->where('deleted', 0)->orderBy('name', 'ASC')->get()
        ]);
    }
}
