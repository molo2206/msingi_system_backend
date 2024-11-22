<?php

namespace App\Helpers;

use App\Models\Company;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class CompanyAuth
{
    public static function company(Request $request)
    {
        $token = $request->header('CompanyAuthorization');
        $accessToken = PersonalAccessToken::findToken($token);
        $company = Company::find($accessToken->tokenable_id);
        return $company;
    }
}
