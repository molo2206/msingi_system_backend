<?php

namespace App\Http\Controllers;

use App\Models\Plans;
use Illuminate\Http\Request;

class PlansController extends Controller
{
    public function index()
    {
        //All modules
        $plan = Plans::with('modules.ressource')->where('deleted', 0)->get();
        return response()->json([
            'code' => 200,
            'message' => 'Plan retrieved successfully',
            'data' => $plan
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:plans',
            'monthly_price' => 'required',
            'yearly_price' => 'required',
            'icon' => 'required'
        ]);
        $plan = Plans::create($request->all());
        if ($plan) {
            $plan->modules()->detach();
            foreach ($request->modules as $item) {
                $plan->modules()->attach([$plan->id =>
                [
                    'module_id' => $item,
                ]]);
            }
        }
        return response()->json([
            'code' => 201,
            'message' => 'Plan created successfully',
        ]);
    }

    public function update(Request $request, $id)
    {
        $plan = Plans::find($id);
        if ($plan->name !== $request->name) {
            $request->validate([
                'name' => 'required|unique:plans'
            ]);
            $plan->name = $request->name;
        }
        $plan->save();
        if ($plan) {
            $plan->modules()->detach();
            foreach ($request->modules as $item)
            {
                $plan->modules()->attach([$plan->id =>
                [
                    'module_id' => $item,
                ]]);
            }
        }
        return response()->json([
            'code' => 200,
            'message' => 'Plan updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $plan = Plans::find($id);
        if ($plan) {
            $plan->deleted = 1;
            $plan->save();
        }
        return response()->json([
            'code' => 200,
            'message' => 'Plan deleted successfully',
            'data' => $plan->with('modules.ressource')->where('status', 1)
                ->where('deleted', 0)->orderBy('name', 'ASC')->get()
        ]);
    }

    public function status(Request $request, $id)
    {
        $request->validate([
            'status' => 'required'
        ]);
        $plan = Plans::find($id);
        if ($plan) {
            $plan->status = $request->status;
            $plan->save();
        }
        return response()->json([
            'code' => 200,
            'message' => 'Status updated!',
            'data' => $plan->with('modules.ressource')->where('status', 1)
                ->where('deleted', 0)->orderBy('name', 'ASC')->get()
        ]);
    }
}
