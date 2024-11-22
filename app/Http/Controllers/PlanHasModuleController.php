<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlanHasModuleController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'plan_id' => 'required',
            'module_id' => 'required',
        ]);

        // Create a new plan_has_module in the database
    }
}
