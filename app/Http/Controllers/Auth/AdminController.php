<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function register(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:6|confirmed'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => $validate->errors()
            ], 400);
        }

        try {

            $admin = Admin::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            if (!$admin) {
                return response()->json([
                    'message' => "Register Fail",
                ], 500);
            }

            return response()->json([
                'message' => "Create Register Success",
            ], 201);
        } catch (Exception $e) {

            return response()->json([
                'message' => "Register Fail"
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => $validate->errors()
            ], 400);
        }
        try {

            $admin = Admin::where('email', $request->email)->first();

            if (!$admin || !Hash::check($request->password, $admin->password)) {
                return response()->json([
                    'message' => 'Invalid credentials',
                ], 401);
            }

            $token = $admin->createToken('AdminTokenPhitTl')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Login Fail',
            ]);
        }
    }
}
