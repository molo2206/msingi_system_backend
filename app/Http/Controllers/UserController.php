<?php

namespace App\Http\Controllers;

use App\Mail\ForgetPasswordEmail;
use App\Mail\NewAccountEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PragmaRX\Google2FA\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UserController extends Controller
{
    //Register logic here
    public function Register(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|unique:users',
            'phone' => 'required|unique:users',
            'code' => 'required',
            'password' => 'required'
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
            'password' => Hash::make($request->password),
            'provider' => $request->provider,
            'email_verified_at' => $request->provider ? Carbon::now() : null,
        ];
        // Save the user to the database and send an email notification to the user
        $current_user = User::create($user);
        // Generate a token

        $data = [
            'account' => "Here is your account!",
            'name' => $current_user->name . ' ' . $current_user->prename . ' ' . $current_user->last_name,
            'email' => $current_user->email,
            'password' => $request->password,
        ];
        // Send an email notification to the user
        if (!$request->provider) {
            $code = ImageController::generateCode();
            $token = ImageController::generateLicenseKey([
                'expires' => ImageController::addMinutes(60),
                'code' => $code,
                'user_id' => $current_user->id
            ]);
            $current_user->userverify()->UpdateOrCreate(
                ["user_id" => $current_user->id],
                ['token' => $token]
            );
            $data = [
                "code" => $code,
                'account' => "Here is your account!",
                'name' => $current_user->name . '' . $current_user->prename . '' . $current_user->last_name,
                'email' => $current_user->email,
                'password' => $request->password,
            ];
            Mail::to($current_user->email)->send(new ForgetPasswordEmail('Forgot password Request', $data));
            return response()->json([
                'massage' => "Votre code de validation à ete envoyer sur votre email!" . $current_user->email,
                'data' => $token
            ], 201);
        }
        Mail::to($request->email)->send(new NewAccountEmail('Register new account Request', $data));
        // Return a success response
        $token = $current_user->createToken("accessToken")->plainTextToken;
        return response()->json([
            'message' => 'Votre compte à été créer avec succès!',
            'code' => 'success',
            'data' =>  User::with('company.fonction.departement')->find($current_user->id),
            'access_token' => $token,
        ], 200);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        // Attempt to authenticate the user
        $google2fa = new Google2FA();
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'message' => 'Invalid credentials.',
                'code' => 401
            ], 401);
        } else {
            // Return an error response if the authentication fails
            $user = User::with('company.fonction.departement')->find(Auth::user()->id);
            if (!$user->email_verified_at) {
                $code = ImageController::generateCode();
                $token = ImageController::generateLicenseKey([
                    'expires' => ImageController::addMinutes(60),
                    'code' => $code,
                    'user_id' => $user->id
                ]);
                $user->userverify()->UpdateOrCreate(
                    ["user_id" => $user->id],
                    ['token' => $token]
                );
                $data = [
                    "code" => $code,
                    'account' => "Here is your account!",
                    'name' => $user->name . '' . $user->prename . '' . $user->last_name,
                    'email' => $user->email,
                    'password' => $request->password,
                ];
                Mail::to($user->email)->send(new ForgetPasswordEmail('Forgot password Request', $data));
                return response()->json([
                    'massage' => "Votre code de validation à ete envoyer sur votre email!" . $user->email,
                    'data' => $token
                ], 201);
            }
            $token = $user->createToken('access_token', ['user']);
            if ($user->google2fa_secret) {
                $qrCodeUrl = $google2fa->getQRCodeUrl(
                    config('app.name'),
                    $user->email,
                    $user->google2fa_secret
                );
                return response()->json([
                    'message' => 'User authenticated successfully.',
                    'code' => 202,
                    'data' => $user,
                    'qr_code_url' => $qrCodeUrl,
                    'access_token' => $token->plainTextToken,
                    'token_type' => 'Bearer',
                ], 202);
            }
            // $user->tokens()->delete();
            return response()->json([
                'message' => 'User authenticated successfully.',
                'code' => 200,
                'data' => $user,
                'access_token' => $token->plainTextToken,
                'token_type' => 'Bearer',
            ]);
        }
    }
    public function getProfile(Request $request)
    {
        $user = User::with('company.fonction.departement')->find(Auth::user()->id);
        return response()->json([
            'message' => 'Profile retrieved successfully.',
            'code' => 200,
            'data' => $user
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json([
            'message' => 'Logged out successfully.',
            'code' => 200
        ], 200);
    }

    public function forgetPasswordRequest(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => ['email' => ['Account with this email not found.']]
            ], 422);
        }

        $code = ImageController::generateCode();
        $user->remember_token = $code;
        $user->save();

        $data = [
            'name' => $user->first_name . ' ' . $user->last_name,
            'code' => $code
        ];

        Mail::to($user->email)->send(new ForgetPasswordEmail('Forgot password Request', $data));

        return response()->json([
            'message' => 'We have sended code to your email.'
        ], 200);
    }

    public function verifyAndChangePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|integer',
            'password' => 'required|min:8|confirmed'
        ]);

        $user = User::where('email', $request->email)
            ->where('remember_token', $request->code)
            ->first();
        if (!$user) {
            return response()->json([
                'errors' => ['code' => ['Invalid otp.']]
            ], 422);
        }

        $user->remember_token = null;
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json([
            'message' => 'Password changed successfully.'
        ], 200);
    }

    public function verifyValidateAccount(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'code' => 'required'
        ]);

        $validSignature = ImageController::decodeLicenseKey($request->token);
        // Vérifier si la signature est valide
        if (hash_equals($validSignature['signature'], $validSignature['decoded'])) {
            $data = json_decode($validSignature['dataString'], true);

            $user = User::with('company.fonction.departement')->find($data['user_id']);
            if (!$user) {
                return response()->json([
                    "message" => "Utilisateur non trouvé!",
                ], 404);
            }
            if ($user->email_verified_at) {
                return response()->json([
                    "message" => "Ce compte est déjà validé, vous pouvez vous connectez!",
                ], 422);
            }
            if (ImageController::isLicenseExpired($data)) {
                return response()->json([
                    "message" => "Votre code a expiré!",
                ], 403);
            } else {
                if ($request->code !== $data['code']) {
                    return response()->json([
                        "message" => "Code incorrect!",
                    ], 403);
                }
                $user->update([
                    'email_verified_at' => Carbon::now()
                ]);
                $user->tokens()->delete();
                $token = $user->createToken('access_token', ['user']);
                return response()->json([
                    'code' => 200,
                    'data' => $user,
                    'access_token' => $token->plainTextToken,
                    'token_type' => 'Bearer',
                ]);
            }
        } else {
            return response()->json([
                "message" => "Votre code a expiré!",
            ], 403);
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'old_password' => 'required|min:8'
        ]);
        $user = $request->user();
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                "message" => "Ancien mot de passe incorrect!",
                "code" => 422
            ], 422);
        }
        $user->password = Hash::make($request->current_password);
        $user->save();
        return response()->json([
            'message' => 'Password changed successfully.'
        ], 200);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'prename' => 'required',
            'last_name' => 'required',
        ]);


        $user = User::with('company.fonction.departement')->find(Auth::user()->id);
        if ($user->email !== $request->email) {
            $request->validate([
                'email' => 'required|email|unique:users'
            ]);
            $user->email = $request->email;
        }

        if ($user->phone !== $request->phone) {
            $request->validate([
                'phone' => 'required|unique:users'
            ]);
            $user->phone = $request->phone;
        }
        $user->code = $request->code;
        $user->name = $request->name ?? $user->name;
        $user->prename = $request->prename ?? $user->prename;
        $user->last_name = $request->last_name ?? $user->last_name;
        $user->gender = $request->gender ?? $user->gender;
        $user->date_of_birth = $request->date_of_birth ?? $user->date_of_birth;
        $user->address = $request->address ?? $user->address;
        $user->country = $request->country ?? $user->country;
        $user->city = $request->city ?? $user->city;
        $user->profile = $request->profile ?? $user->profile;
        $user->longitude = $request->longitude ?? $user->longitude;
        $user->latitude = $request->latitude ?? $user->latitude;
        $user->save();
        return response()->json([
            'message' => 'Profile updated successfully.',
            'data' => $user
        ]);
    }
    public function editProfilePicture(Request $request)
    {
        $request->validate([
            "profile" => "required"
        ]);
        $user = $request->user();
        $user->profile = $request->profile;
        $user->save();
        return response()->json([
            "message" => 'Profile picture updated successfully',
            "status" => 1,

        ], 200);
    }
}
