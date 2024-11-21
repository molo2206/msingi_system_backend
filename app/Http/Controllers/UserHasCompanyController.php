<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Fonction;
use App\Models\User_has_company;
use Illuminate\Http\Request;
use PDO;

class UserHasCompanyController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'fonction_id' => 'required',
            'company_id' => 'required',
            'user_id' => 'required',
        ]);

        // Create a new user_has_company in the database
        if (Company::find($request->company_id)) {
            // Check if the user already has a role in this company
            $user_has_company = User_has_company::where('user_id', $request->user()->id)
                ->where('company_id', $request->company_id)
                ->first();

            if ($user_has_company) {
                return response()->json(['error' => 'User already has a role in this company.'], 402);
            }
            // check fonction

            $fonction = Fonction::find($request->fonction_id);
            if (!$fonction) {
                return response()->json(['error' => 'Function not found.'], 402);
            }

            User_has_company::create([
                'user_id' => $request->user()->id,
                'company_id' => $request->company_id,
                'fonction_id' => $request->fonction_id,
            ]);
            return response()->json(
                [
                    'message' => 'User has been assigned to the company successfully.',
                    'code' => 200
                ],
                202
            );
        } else {
        }
    }
}
