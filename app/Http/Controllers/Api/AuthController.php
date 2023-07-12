<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $validate = Validator::make($credentials, ['email' => 'required', 'password' => 'required']);
            if ($validate->fails()) {
                return response()->json(['success' => false, 'validations' => $validate->errors(), 'code' => 400], 400);
            }

            $token = Auth::attempt($credentials);

            if ($token) {
                $user = User::where('email', $credentials['email'])->first();

                $data = [
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth()->factory()->getTTL() * 60,
                    'authState' => $user
                ];
                return response()->json(['status' => true, ...$data]);
            } else {
                return response()->json(['success' => false, 'message' => "Invalid email dan password", 'code' => 401], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "Internal server error", 'code' => 500], 500);
        }
    }

    public function me()
    {
        try {
            return response()->json(['status' => true, 'me' => auth()->user()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "Internal server error", 'code' => 500], 500);
        }
    }

    public function logout()
    {
        try {
            auth()->logout();
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "Internal server error", 'code' => 500], 500);
        }
    }
}
