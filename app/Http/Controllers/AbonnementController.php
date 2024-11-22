<?php

namespace App\Http\Controllers;

use App\Models\Abonnement;
use App\Models\Company;
use Illuminate\Http\Request;

class AbonnementController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'company_id' => 'required',
            'month' => 'required',
        ]);
        // Create the relationship between the company and the abonnement
        $expire = ImageController::addMonth($request->month);
        $company = Company::where('id', $request->company_id)->first();
        if (!$company) {
            return response()->json([
                'error' => 'Company not found!',
            ], 422);
        }
        //Create the new abonnement in the database
        $token = ImageController::generateLicenseKey([
            "company_id" => $company->id,
            'expires' => $expire,
        ]);

        Abonnement::create([
            'company_id' => $request->company_id,
            'expires' => $expire,
            'token' => $token,
        ]);
        return response()->json([
            'message' => 'Abonnement created successfully.',
            'data' => $company->with('abonnement')->get()
        ], 200);
    }
    // public function oneAbonnement

    public function getAbonnement() {}
}
