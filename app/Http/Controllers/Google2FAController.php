<?php

namespace App\Http\Controllers;

use App\Models\User;
use Closure;
use PragmaRX\Google2FA\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Google2FAController extends Controller
{
    public function enableTwoFactorAuthentication(Request $request)
    {
        $user = $request->user();
        // Crée une instance de Google2FA
        $google2fa = new Google2FA();
        // Génère une clé secrète
        $secretKey = $google2fa->generateSecretKey();
        // Sauvegarde la clé secrète dans la base de données

        if ($user->google2fa_secret) {
            $user->google2fa_secret = null;
        } else {
            $user->google2fa_secret = $secretKey;
        }
        $user->save();
        // Retourne l'URL du QR code
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secretKey
        );

        return response()->json([
            'qr_code_url' => $qrCodeUrl,
            'manual_entry_key' => $secretKey,
        ]);
    }

    public function verifyTwoFactorCode(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric',
        ]);

        $user = User::find(Auth::user()->id);
        if (!$user->google2fa_secret) {
            return response()->json(['message' => 'Votre code de sécurité à expirer!'], 422);
        }
        $company = count($user->company) ? $user->company[0]: null;
        $tokenComp = $company->createToken('access_token');
        $token = $user->createToken('access_token', ['user']);
        $google2fa = new Google2FA();
        $isValid = $google2fa->verifyKey($user->google2fa_secret, $request->code);
        if ($isValid) {

            return response()->json([
                'message' => 'User authenticated successfully.',
                'code' => 202,
                'data' => $user,
                'company_token' => $tokenComp->plainTextToken,
                'access_token' => $token->plainTextToken,
                'company' => $company,
            ], 202);
        }
        return response()->json(['message' => 'Votre code de sécurité n\'est pas correcte!'], 422);
    }
}
