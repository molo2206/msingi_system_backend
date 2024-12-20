<?php

namespace App\Http\Controllers;

use App\Helpers\CompanyAuth;
use App\Models\Company;
use App\Models\Fonction;
use App\Models\Succursales;
use App\Models\User;
use App\Models\User_has_company;
use App\Models\UserHasCompanyHasSuccursales;
use App\Rules\SuccursalNameValidation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class SuccursalesController extends Controller
{
    public function index(Request $request)
    {
        //All modules
        $company = CompanyAuth::company($request);
        $succursale = Succursales::with('company')->where('deleted', 0)->where('company_id', $company->id)->get();
        return response()->json([
            'code' => 200,
            'message' => 'Succursales retrieved successfully',
            'data' => $succursale
        ], 200);
    }
    public function store(Request $request)
    {
        $company = CompanyAuth::company($request);
        $request->validate([
            'name' => ['required', new SuccursalNameValidation($company->id, $request->name)],
            'email' => 'required',
            'phone' => 'required',
            'adresse' => 'required',
        ]);

        $company->succursale()->create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'adresse' => $request->adresse,
            'company_id' => $company->id,
        ]);
        return response()->json([
            'message' => 'Succursale created successfully',
        ], 200);
    }
    public function update(Request $request, $id)
    {
        $company = CompanyAuth::company($request);
        $succursale = Succursales::where('id', $id)->where('company_id',  $company->id)
            ->where('deleted', 0)->first();
        if ($succursale->name !== $request->name) {
            $request->validate([
                'name' => 'required'
            ]);
            $succursale->name = $request->name;
        }
        if ($succursale->email !== $request->email) {
            $request->validate([
                'email' => 'required|unique:succursales'
            ]);
            $succursale->email = $request->email;
        }
        if ($succursale->phone !== $request->phone) {
            $request->validate([
                'phone' => 'required|unique:succursales'
            ]);
            $succursale->phone = $request->phone;
        }
        if ($succursale->adresse !== $request->adresse) {
            $request->validate([
                'adresse' => 'required|unique:succursales'
            ]);
            $succursale->adresse = $request->adresse;
        }
        $succursale->update($request->all());
        return response()->json([
            'code' => 200,
            'message' => 'Ressource updated successfully',
            'data' => $succursale->with('company')->where('status', 1)
                ->where('deleted', 0)->orderBy('name', 'ASC')->get()
        ]);
    }
    public function destroy(Request $request, $id)
    {
        $company = CompanyAuth::company($request);
        $succursale = Succursales::where('id', $id)->where('company_id', $company->id)->where('deleted', 0)->first();
        if ($succursale) {
            $succursale->deleted = 1;
            $succursale->save();
        }
        return response()->json([
            'code' => 200,
            'message' => 'Succursale deleted successfully',
            'data' => $succursale->with('company')->where('status', 1)
                ->where('deleted', 0)->orderBy('name', 'ASC')->get()
        ]);
    }
    public function status(Request $request, $id)
    {
        $request->validate([
            'status' => 'required',
        ]);
        $company = CompanyAuth::company($request);
        $succursale = Succursales::where('id', $id)->where('company_id', $company->id)->where('deleted', 0)->first();
        if ($succursale) {
            $succursale->status = $request->status;
            $succursale->save();
        }
        return response()->json([
            'code' => 200,
            'message' => 'Status updated!',
            'data' => $succursale->with('company')->where('status', 1)
                ->where('deleted', 0)->orderBy('name', 'ASC')->get()
        ]);
    }

    public function Affectation(Request $request)
    {
        $company = CompanyAuth::company($request);
        $request->validate([
            'type' => 'required',
            'fonction_id' => 'required',
            'succursale_id' => 'required',
            'permissions' => "required|array|min:1"
        ]);

        // Create a new user_has_company in the database
        if ($request->type == "new") {
            // Validate the request data
            $request->validate([
                'email' => 'required|unique:users',
                'name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'code' => 'required',
                'phone' => 'required|unique:users',
            ]);
            // Create a new user
            $user = [
                'name' => $request->name,
                'prename' => $request->prename,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'country' => $request->country,
                'city' => $request->city,
                'code' => $request->code,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make(12345678),
                'provider' => $request->provider,
                'email_verified_at' => $request->provider ? Carbon::now() : null,
            ];
            // Save the user to the database and send an email notification to the user
            $current_user = User::create($user);
            // check fonction
            $fonction = Fonction::find($request->fonction_id);
            if (!$fonction) {
                return response()->json(['error' => 'Function not found.'], 402);
            }

            $user_has_company = User_has_company::create([
                'user_id' => $current_user->id,
                'company_id' => $company->id,
                'fonction_id' => $request->fonction_id,
            ]);

            $roles = UserHasCompanyHasSuccursales::create([
                'hasuser_id' => $user_has_company->id,
                'succursale_id' => $request->succursale_id,
            ]);

            if ($roles) {
                $roles->permission()->detach();
                foreach ($request->permissions as $item) {
                    $roles->permission()->attach([$item['ressource_id'] => ['create' => $item['create'], 'read' => $item['read'], 'update' => $item['update'], 'delete' => $item['delete']]]);
                }
                return response()->json([
                    "message" => trans('L\'affectation s\'est déroulée avec succès.!'),
                ], 200);
            }
            return response()->json(
                [
                    'message' => 'User has been assigned to the company successfully.',
                    'code' => 200
                ],
                202
            );
        } else {
            // Validate the request data
            $request->validate([
                'user_id' => 'required'
            ]);
            // Find the user in the database
            $user = User::find($request->user_id);
            if (!$user) {
                return response()->json(['error' => 'User not found.'], 422);
            }
            if ($user->company()->find($company->id)) {
                return response()->json([
                    'message' => 'L’utilisateur est déjà affecté à cette entreprise!',
                ], 422);
            }
            $user_has_company = User_has_company::create([
                'user_id' => $request->user_id,
                'company_id' => $company->id,
                'fonction_id' => $request->fonction_id,
            ]);
            $roles = UserHasCompanyHasSuccursales::create([
                'hasuser_id' => $user_has_company->id,
                'succursale_id' => $request->succursale_id,
            ]);

            if ($roles) {
                $roles->permission()->detach();
                foreach ($request->permissions as $item) {
                    $roles->permission()->attach([$item['ressource_id'] => ['create' => $item['create'], 'read' => $item['read'], 'update' => $item['update'], 'delete' => $item['delete']]]);
                }
                return response()->json([
                    "message" => trans('L\'affectation s\'est déroulée avec succès.!'),
                ], 200);
            }
            return response()->json(
                [
                    'message' => 'User has been assigned to the company successfully.',
                    'code' => 200
                ],
                202
            );
        }
    }
    public function updateAffectation(Request $request)
    {
        $company = CompanyAuth::company($request);
        $request->validate([
            'hasuser_id' => 'required',
            'succursale_id' => 'required',
            'permissions' => "required|array|min:1"
        ]);
        $user_has_company = User_has_company::where('id', $request->hasuser_id)->first();
        if (!$user_has_company) {
            return response()->json(['message' => 'Cet utilisateur n\'existe pas dans le système!.'], 422);
        }

        $succursale = Succursales::where('id', $request->succursale_id)->first();
        if (!$succursale) {
            return response()->json(['message' => 'Cette succursale n\'existe pas dans le système!.'], 422);
        }

        $affectation = UserHasCompanyHasSuccursales::where('hasuser_id', $user_has_company->id)->first();
        if ($affectation) {
            $affectation->hasuser_id = $request->hasuser_id;
            $affectation->succursale_id = $request->succursale_id;
            $affectation->save();
            $affectation->permission()->detach();
            foreach ($request->permissions as $item) {
                $affectation->permission()->attach([$item['ressource_id'] => ['create' => $item['create'], 'read' => $item['read'], 'update' => $item['update'], 'delete' => $item['delete']]]);
            }
            return response()->json([
                'code' => 200,
                'message' => 'Affectation mise à jour avec succès!',
                'data' => Company::with(['users.item', 'users.succursales.item', 'users.succursales.permission'])->find($company->id)
            ]);
            // return response()->json(['message' => 'Cette affectation n\'existe pas dans le système!'], 422);
        }
    }

    public function statusAffectation(Request $request)
    {
        $company = CompanyAuth::company($request);
        $request->validate([
            'id' => 'required',
            'status' => 'required',
        ]);
        $affectation = UserHasCompanyHasSuccursales::where('id', $request->id)
            ->where('deleted', 0)->first();
        if ($affectation) {
            $affectation->status = $request->status;
            $affectation->save();
        }
        return response()->json([
            'code' => 200,
            'message' => 'Statut mis à jour avec succès!',
            'data' => Company::with(['users.item', 'users.succursales.item', 'users.succursales.permission'])->find($company->id)
        ]);
    }
    public function deleteAffectation(Request $request)
    {
        $company = CompanyAuth::company($request);
        $request->validate([
            'id' => 'required',
        ]);
        $affectation = UserHasCompanyHasSuccursales::where('id', $request->id)
            ->where('deleted', 0)->first();
        if ($affectation) {
            $affectation->deleted = 1;
            $affectation->save();
        }
        return response()->json([
            'code' => 200,
            'message' => 'Affectation supprimée avec succès!',
            'data' => Company::with(['users.item', 'users.succursales.item', 'users.succursales.permission'])->find($company->id)
        ]);
    }
}
