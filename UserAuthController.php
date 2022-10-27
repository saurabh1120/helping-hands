<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\User;
use App\DonatedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserAuthController extends Controller
{


    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email_or_phone' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user = User::where(['email' => $request['email_or_phone']])->orWhere('phone', $request['email_or_phone'])->first();

        if (isset($user)) {

            $data = [
                'email' => $user->email,
                'password' => $request->password
            ];
            $reward_points = DonatedItem::where('user_id',$user->id)->where('isAccepted',1)->count();
            $user['reward_points']=$reward_points;

            if (auth()->attempt($data)) {

                $token = auth()->user()->createToken('HelpingHandsAuth')->accessToken;
                return response()->json(['token' => $token, 'user' => $user], 200);
            }
        }

        $errors = [];
        array_push($errors, ['code' => 'auth-001', 'message' => 'Invalid credential.']);
        return response()->json([
            'errors' => $errors
        ], 401);
    }

    public function registration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|min:6',
        ], [
            'name.required' => 'The Name field is required.',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->mobile,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('HelpingHandsAuth')->accessToken;

        return response()->json(['token' => $token, 'user' => $user], 200);
    }
}
