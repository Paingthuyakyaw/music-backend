<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => $validate->errors()
            ], 400);
        }

        try {
            $user = new User();
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            if ($user) {
                return response()->json([
                    'message' => "Successfully create"
                ], 201);
            }

            return response()->json([
                'message' => 'Fail create'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Fail create',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 400);
        }

        try {
            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $user->tokens()->delete();
                $token = $user->createToken('userAuthPhitPrTl', ['role:user'])->plainTextToken;
                return response()->json([
                    'message' => 'Login successfully',
                    'token' => $token,

                ], 201);
            }

            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Login fail',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    // ========= forgot_password =========== //

    public function forgot_password(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where("email", $request->email)->first();


        if (!$user) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'

            ], 400);
        }


        $token = Str::random(50);
        $dateTime = Carbon::now()->format('Y-m-d H:i:s');
        $resetData =  PasswordReset::updateOrCreate(
            [
                'email' => $request->email
            ],
            [
                'email' => $request->email,
                'token' => $token,
                'create_at' => $dateTime
            ]

        );

        return response()->json([
            "message" => "Reset your password",
            "token" => $resetData->token
        ], 200);
    }

    public function reset_password(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()
            ], 400);
        }


        $user = User::where("email", $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'

            ], 400);
        }


        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            "message" => "Reset password successfully"
        ], 200);
    }
}
