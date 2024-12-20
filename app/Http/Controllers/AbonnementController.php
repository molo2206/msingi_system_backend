<?php

namespace App\Http\Controllers;

use App\Helpers\CompanyAuth;
use App\Models\Abonnement;
use App\Models\Company;
use App\Models\Plans;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbonnementController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'company_id' => 'required',
            'month' => 'required',
            'plan_id' => 'required',
        ]);
        $company = Company::where('id', $request->company_id)->first();
        if (!$company) {
            return response()->json([
                'message' => 'Company not found!',
            ], 422);
        }
        $abonnement = $company->abonnement()->orderBy('created_at', 'DESC')->first();
        if ($abonnement) {
            if ($abonnement->expires <= Carbon::now() && $abonnement->plans_id !== $request->plan_id) {
                // Create the relationship between the company and the abonnement
                $plan = Plans::where('id', $request->plan_id)->first();
                if ($plan) {
                    // Calculate the total price for the selected plan and month
                    if ($request->month < 12) {
                        $total_price = $plan->monthly_price * $request->month;
                    }
                    if ($request->month >= 12) {
                        $total_price = $plan->yearly_price / 12 * $request->month;
                    }
                    $expire = ImageController::addMonth($request->month);
                    //Create the new abonnement in the database
                    $token = ImageController::generateLicenseKey([
                        "company_id" => $company->id,
                        'expires' => $expire,
                    ]);

                    Abonnement::create([
                        'company_id' => $request->company_id,
                        'expires' => $expire,
                        'token' => $token,
                        'plans_id' => $request->plan_id,
                        'total_price' => $total_price,
                        'month' => $request->month
                    ]);

                    $company->trial = null;
                    $company->save();
                    return response()->json([
                        'message' => 'Abonnement created successfully.',
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Plan not found!',
                    ], 422);
                }
            } else {
                return response()->json([
                    'message' => 'Un plan d\'abonnement est toujours en cours pour vous, Si vous souhaitez modifier votre plan, veuillez contacter l\'administrateur sur cette adresse:!' . 'devmolomolo@gmail.com',
                ], 422);
            }
        } else {
            // Create the relationship between the company and the abonnement
            $plan = Plans::where('id', $request->plan_id)->first();
            if ($plan) {
                // Calculate the total price for the selected plan and month
                if ($request->month < 12) {
                    $total_price = $plan->monthly_price * $request->month;
                }
                if ($request->month >= 12) {
                    $total_price = $plan->yearly_price / 12 * $request->month;
                }
                $expire = ImageController::addMonth($request->month);
                //Create the new abonnement in the database
                $token = ImageController::generateLicenseKey([
                    "company_id" => $company->id,
                    'expires' => $expire,
                ]);

                Abonnement::create([
                    'company_id' => $request->company_id,
                    'expires' => $expire,
                    'token' => $token,
                    'plans_id' => $request->plan_id,
                    'total_price' => $total_price,
                ]);

                $company->trial = null;
                $company->save();
                return response()->json([
                    'message' => 'Abonnement created successfully.',
                    // 'data' => $company->with('abonnement')->get()
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Plan not found!',
                ], 422);
            }
        }
    }

    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'company_id' => 'required',
            'month' => 'required',
            'plan_id' => 'required',
        ]);
        $abonnement = Abonnement::find($id);
        if (!$abonnement) {
            return response()->json([
                'message' => 'Abonnement not found!',
            ], 422);
        }
        $company = Company::where('id', $request->company_id)->first();
        if (!$company) {
            return response()->json([
                'error' => 'Company not found!',
            ], 422);
        }
        $plan = Plans::where('id', $request->plan_id)->first();
        if ($plan) {
            // Calculate the total price for the selected plan and month
            if ($request->month < 12) {
                $total_price = $plan->monthly_price * $request->month;
            }
            if ($request->month >= 12) {
                $total_price = $plan->yearly_price / 12 * $request->month;
            }
            $expire = ImageController::addMonth($request->month);
            //Create the new abonnement in the database
            $token = ImageController::generateLicenseKey([
                "company_id" => $company->id,
                'expires' => $expire,
            ]);

            $abonnement->company_id = $request->company_id;
            $abonnement->expires = $expire;
            $abonnement->token = $token;
            $abonnement->plans_id = $request->plan_id;
            $abonnement->total_price = $total_price;
            $abonnement->month = $request->month;
            $abonnement->save();

            return response()->json([
                'message' => 'Abonnement update successfully.',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Plan not found!',
            ], 422);
        }
    }

    public function getAbonnement()
    {
        // Get all abonnements
        $abonnements = Abonnement::with('company', 'plan')->get();
        return response()->json([
            'code' => 200,
            'message' => 'Abonnements retrieved successfully',
            'data' => $abonnements
        ]);
    }
    public function oneAbonnement($id)
    {
        // Get one abonnement
        $abonnement = Abonnement::with('company', 'plan')->where('id', $id)->first();
        return response()->json([
            'code' => 200,
            'message' => 'Abonnement retrieved successfully',
            'data' => $abonnement
        ]);
    }
}
