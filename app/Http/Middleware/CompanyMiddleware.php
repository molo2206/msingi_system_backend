<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class CompanyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('CompanyAuthorization');
        if (!$token) {
            return response()->json([
                'message' => "Vous devez fournir l'authorization de cette compagnie pour accéder à cette ressource!"], 403);
        }

        // Trouver le token dans la base de données
        $accessToken = PersonalAccessToken::findToken($token);
        if(!$accessToken){
            return response()->json(['message' => 'Token invalid!'], 403);
        }
        $company = Company::find($accessToken->tokenable_id);
        if (!$company) {
            return response()->json([
                "message" => "Token invalid!",
            ], 403);
        } else if ($company->trial <= date('Y-m-d')) {
            return response()->json([
                "message" => "Votre période d'essaie a expiré!",
            ], 403);
        }
        return $next($request);
    }
}