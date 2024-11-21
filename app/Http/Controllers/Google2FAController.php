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

    // public function getQRCode(Request $request)
    // {
    //     $user = $request->user();

    //     if (!$user->google2fa_secret) {
    //         return response()->json(['message' => '2FA is not enabled'], 400);
    //     }

    //     $qrCodeUrl = (new Google2FA())->getQRCodeUrl(
    //         config('app.name'),
    //         $user->email,
    //         $user->google2fa_secret
    //     );

    //     return response()->json([
    //         'qr_code' => QrCode::size(200)->generate($qrCodeUrl),
    //     ]);
    // }

    public function verifyTwoFactorCode(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric',
        ]);

        $user = User::with('company.fonction.departement')->find(Auth::user()->id);
        if (!$user->google2fa_secret) {
            return response()->json(['message' => 'Votre code de sécurité à expirer!'], 422);
        }
        $token = $user->createToken('access_token', ['user']);
        $google2fa = new Google2FA();
        $isValid = $google2fa->verifyKey($user->google2fa_secret, $request->code);
        if ($isValid) {

            return response()->json([
                'message' => 'User authenticated successfully.',
                'code' => 202,
                'data' => $user,
                'access_token' => $token->plainTextToken,
                'token_type' => 'Bearer',
            ], 202);
        }
        return response()->json(['message' => 'Votre code de sécurité n\'est pas correcte!'], 422);
    }
}
