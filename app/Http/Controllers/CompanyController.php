<?php

namespace App\Http\Controllers;

use App\Helpers\CompanyAuth;
use App\Models\Abonnement;
use App\Models\Company;
use App\Models\Plans;
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
            'country' => $request->country,
            'city' => $request->city,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
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

        $token = $company->createToken('access_token');
        //Abonnement Trial
        $plan = Plans::where('name', "Business")->first();
        if ($plan) {
            $trial = Carbon::now()->addDays(15)->format('Y-m-d');
            //Create the new abonnement in the database
            $token_comp = ImageController::generateLicenseKey([
                "company_id" => $company->id,
                'expires' => $trial,
            ]);

            Abonnement::create([
                'company_id' => $company->id,
                'expires' => $trial,
                'token' => $token_comp,
                'plans_id' => $plan->id,
                'total_price' => 0,
            ]);
        }
        // Return the company data
        return response()->json([
            'code' => 200,
            'message' => 'Company created successfully.',
            'data' => User::find(Auth::user()->id),
            'company' => $company,
            'company_token' => $token->plainTextToken
        ], 200);
        // Return a success response

    }

    public function update(Request $request)
    {
        $company = CompanyAuth::company($request);
        if (!$company) {
            return response()->json([
                'error' => 'Company not found.'
            ]);
        }
        if ($company->name !== $request->name) {
            $request->validate([
                'name' => 'required'
            ]);
            $company->name = $request->name;
        }
        if ($company->email !== $request->email) {
            $request->validate([
                'email' => 'required'
            ]);
            $company->email = $request->email;
        }
        $company->update($request->all());
        // Return a success response
        return response()->json([
            'message' => 'Company updated successfully',
            'data' =>  $company,
        ], 200);
    }

    public function loginCompany($id)
    {
        // Find the company by id
        $company = Company::find($id);
        if (!$company) {
            return response()->json([
                'error' => 'Company not found.'
            ], 404);
        }
        $token = $company->createToken('access_token');
        // Return the company data
        return response()->json([
            'code' => 200,
            'message' => 'Company selected successfully.',
            'data' => $company,
            'company_token' => $token->plainTextToken
        ], 200);
    }

    public function getUserCompanies()
    {
        // Get the authenticated user
        $user = Auth::user();
        // Return the companies data
        return response()->json([
            'code' => 200,
            'message' => 'Companies retrieved successfully.',
            'data' => $user->company()->with('secteur', 'succursale')->get()
        ], 200);
    }

    public function getAllCompanies()
    {
        // Get all companies
        $companies = Company::with('secteur', 'succursale')->get();
        // Return the companies data
        return response()->json([
            'code' => 200,
            'message' => 'Companies retrieved successfully.',
            'data' => $companies
        ], 200);
    }

    public function getByCompany(Request $request)
    {
        // Get abonnements by company
        $company = CompanyAuth::company($request);
        $abonnements = Abonnement::with('company', 'plan')->where('company_id', $company->id)->get();
        return response()->json([
            'code' => 200,
            'message' => 'Abonnements retrieved successfully',
            'data' => $abonnements
        ]);
    }

    public function getUserCompany(Request $request)
    {
        $company = CompanyAuth::company($request);
        return Company::with(['users.item', 'users.succursales.item','users.succursales.permission'])->find($company->id);
    }

    public function CheckAbonnement()
    {
        // Get the authenticated user
        return response()->json([
            "is_valid" => true,
        ], 200);
    }

    public function edit($id) {}
    public function destroy($id) {}
    public function search(Request $request) {}
}
