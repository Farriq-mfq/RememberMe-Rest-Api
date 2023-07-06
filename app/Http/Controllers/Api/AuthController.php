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
                $data = [
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth()->factory()->getTTL() * 60
                ];
                return response()->json(['status' => true, ...$data]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "Internal server error", 'code' => 500], 500);
        }
    }
}
