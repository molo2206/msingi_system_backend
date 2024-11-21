<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\User_has_company;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'code' => 'required',
            'phone' => 'required',
            'fonction_id' => 'required',
        ]);
        // Create a new company in the database
        $trial = Carbon::now()->addDays(30)->format('Y-m-d');
        $data = [
            "name" => $request->name,
            "description" => $request->description,
            "email" => $request->email,
            "code"  => $request->code,
            "phone" => $request->phone,
            "adresse" => $request->adresse,
            "website" => $request->website,
            "facebook" => $request->facebook,
            "twitter" => $request->twitter,
            "instagram" => $request->instagram,
            "linkedin" => $request->linkedin,
            "id_nat" => $request->id_nat,
            "rccm" => $request->rccm,
            "num_impot" => $request->num_impot,
            "banner" => $request->banner,
            "logo" => $request->logo,
            "trial" => $trial,
            "secteur_id" => $request->secteur_id,
            "nbr_users" => $request->nbr_users,
            "fonction_id" => $request->fonction_id,
        ];
        $company = Company::create($data);
        // Create a new user_has_company in the database
        $user = Auth::user();
        $user_has_company = User_has_company::where('user_id', $request->user()->id)
            ->where('company_id', $request->company_id)
            ->first();

        if ($user_has_company) {
            return response()->json(['error' => 'User already in this company.'], 402);
        }
        User_has_company::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'fonction_id' => $request->fonction_id,
        ]);

        // Return a success response
        return response()->json([
            'message' => 'Company created successfully',
            'data' => User::with('company.fonction.departement')->find(Auth::user()->id),
        ], 200);
    }

    public function update(Request $request, $id)
    {
        // Update the company in the database
        $company = Company::find($id);
        if(!$company){
            return response()->json([
                'error' => 'Company not found.'
            ]);
        }
        if ($company->name !== $request->name) {
            $request->validate([
                'name' => 'required|name|unique:companies'
            ]);
            $company->name = $request->name;
        }
        if ($company->email !== $request->email) {
            $request->validate([
                'email' => 'required|email|unique:companies'
            ]);
            $company->email = $request->email;
        }

        if ($company->phone !== $request->phone) {
            $request->validate([
                'phone' => 'required|phone|unique:companies'
            ]);
            $company->phone = $request->phone;
        }
        $company->update($request->all());
        // Return a success response
        return response()->json([
            'message' => 'Company updated successfully',
            'data' => User::with('company.fonction.departement')->find(Auth::user()->id),
        ], 200);
    }

    public function edit($id) {}
    public function destroy($id) {}
    public function search(Request $request) {}
}
