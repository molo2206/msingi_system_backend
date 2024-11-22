<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class ImageController extends Controller
{
    public static function uploadImageUrl($field, $destination)
    {
        if ($field) {
            $image = Image::make($field);
            $png_url = md5(rand(1000, 10000)) . ".png";
            $width = $image->width();
            $height = $image->height();
            $image->resize($width / 2, $height / 2); // Redimensionnement de l'image à 120 x 80 px
            $image->save(public_path() . $destination . $png_url);
            return env('APP_URL') . $destination . $png_url;
        }
    }

    public static function uploadMultipleImage($field, $destination)
    {
        $images = [];
        if ($field) {
            foreach ($field as $file) {
                $image = Image::make($file);
                $png_url = md5(rand(1000, 10000)) . ".png";
                $width = $image->width();
                $height = $image->height();
                //$image->resize($width / 2, $height / 2); // Redimensionnement de l'image à 120 x 80 px
                $image->save(public_path() . $destination . $png_url);
                array_push($images, env('APP_URL') . $destination . $png_url);
            }
            return $images;
        }
    }
    public static function removeImageUrl($url)
    {
        $path = str_replace(env('APP_URL'), "", $url);
        if (File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
    public static function removeImage($field, $destination)
    {
        if (File::exists(public_path(env('APP_URL') . $destination . $field))) {
            File::delete(public_path(env('APP_URL') . $destination . $field));
        }
    }
    public static function generateCode()
    {
        $characters = '0123456789';
        $charactersNumber = strlen($characters);
        $code = '';
        while (strlen($code) < 4) {
            $position = rand(0, $charactersNumber - 1);
            $character = $characters[$position];
            $code = $code . $character;
        }
        return $code;
    }

    public static function generateToken()
    {
        $characters = '0123456789';
        $charactersNumber = strlen($characters);
        $code = '';
        while (strlen($code) < 10) {
            $position = rand(0, $charactersNumber - 1);
            $character = $characters[$position];
            $code = $code . $character;
        }
        return $code;
    }

    public static function generateLicenseKey($data)
    {
        $dataString = json_encode($data);
        $signature = hash_hmac('sha256', $dataString, env('KEY_LICENCE'));
        return base64_encode($dataString . '.' . $signature);
    }
    public static function decodeLicenseKey($data)
    {
        $decoded = base64_decode($data);
        list($dataString, $signature) = explode('.', $decoded);
        // Vérifier la signature avec le secret
        return [
            'decoded' => hash_hmac('sha256', $dataString, env('KEY_LICENCE')),
            'signature' => $signature,
            'dataString' => $dataString
        ];
    }

    public static function isLicenseExpired($data)
    {
        // Simulate the expiration of the license
        return now()->gt(Carbon::parse($data['expires']));
    }

    public static function addMinutes($minute)
    {
        $date = new DateTime();
        $date->modify('+' . $minute . ' minutes');
        return $date->format('Y-m-d H:i:s');
    }

    public static function addMonth($mois)
    {
        $date = Carbon::now();
        $newDate = $date->addMonths($mois);
        return $newDate->format('Y-m-d');
    }
}
