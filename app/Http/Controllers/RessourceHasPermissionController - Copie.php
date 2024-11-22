<?php

namespace App\Http\Controllers;

use App\Models\Ressource_Has_Permission;
use App\Models\User;
use App\Models\User_has_company;
use Illuminate\Http\Request;

class RessourceHasPermissionController extends Controller
{
    public function Affectation(Request $request)
    {
        $request->validate([
            "user_id" => "required",
            "permissions" => "required|array|min:1"
        ]);

        $role = User_has_company::where('user_id',$request->user_id)->first();
        if ($role) {
            $role->permission()->detach();
            foreach ($request->permissions as $item) {
                $role->permission()->attach([$item['ressource_id'] => ['create' => $item['create'], 'read' => $item['read'], 'update' => $item['update'], 'delete' => $item['delete']]]);
            }
            return response()->json([
                "message" => trans('messages.saved'),
            ], 200);
        } else {
            return response()->json([
                "message" => trans('messages.notFound')
            ]);
        }
    }

    public function assignPermissions(Request $request)
    {
        $request->validate([
            "user_id" => "required",
            "permissions" => "required|array|min:1"
        ]);

        $role = User_has_company::where('status', 1)->find($request->user_id);
        if ($role) {
            $role->permission()->detach();
            foreach ($request->permissions as $item) {
                $role->permission()->attach([$item['ressource_id'] => ['create' => $item['create'], 'read' => $item['read'], 'update' => $item['update'], 'delete' => $item['delete']]]);
            }
            return response()->json([
                "message" => trans('messages.saved'),
            ], 200);
        } else {
            return response()->json([
                "message" => trans('messages.notFound')
            ]);
        }
    }
}
