<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $validate = Validator::make($credentials, ['email' => 'required|email', 'password' => 'required'], ['email.required' => 'email harus di isi', 'email.email' => 'email tidak valid', 'password.required' => 'password harus di isi']);
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

    public function register(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password', 'confirm_password', 'nama');
            $validate = Validator::make($credentials, ['email' => 'required|email|unique:users,email', 'password' => 'required', 'confirm_password' => 'required|same:password', 'nama' => 'required'], [
                'email.required' => 'email harus di isi', 'email.email' => 'email tidak valid', 'password.required' => 'password harus di isi', 'confirm_password.required' => 'Konfirmasi password harus di isi', 'confirm_password.same' => 'konfirmasi password harus sama dengan password', 'email.unique' => 'email sudah tersedia',
                'nama.required' => 'nama harus di isi'
            ]);
            if ($validate->fails()) {
                return response()->json(['success' => false, 'validations' => $validate->errors(), 'code' => 400], 400);
            }
            $data = [
                'name' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ];
            $user = User::create($data);

            if ($user) {
                return response()->json(['status' => true, 'user' => $user]);
            } else {
                return response()->json(['success' => false, 'message' => "failed create user", 'code' => 400], 400);
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
    public function refresh()
    {
        try {
            $token = auth()->refresh();
            $data = [
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
            ];
            return response()->json(['status' => true, ...$data]);
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
