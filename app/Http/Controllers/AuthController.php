<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     */
  public function register(Request $request)
{
    $data = $request->validate([
        'name'            => 'required|string|max:255',
        'phone'           => 'nullable|string|max:20',
        'username'        => 'required|string|max:50|unique:users',
        'email'           => 'required|email|unique:users',
        'password'        => 'required|string|min:6|confirmed',
        'type'            => 'nullable|in:admin,student',
        'birthdate'       => 'nullable|date',
        'address'         => 'nullable|string|max:255',
    ]);

    DB::beginTransaction();
    try {
        $user = User::create([
            'name'            => $data['name'],
            'phone'           => $data['phone']       ?? null,
            'username'        => $data['username'],
            'email'           => $data['email'],
            'password'        => Hash::make($data['password']),
            'type'            => $data['type']        ?? 'student',
            'registered_at'   => now(),
            'birthdate'       => $data['birthdate']   ?? null,
            'address'         => $data['address']     ?? null,
        ]);

        event(new Registered($user));

        $token = $user->createToken('api_token')->plainTextToken;
        DB::commit();

        return response()->json([
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 201);

    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'حدث خطأ أثناء التسجيل. حاول مرة أخرى.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}


    /**
     * Handle user login.
     */
     public function login(Request $request)
{
    $fields = $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    $user = User::where('username', $fields['username'])->first();

    if (!$user || ! Hash::check($fields['password'], $user->password)) {
        return response()->json([
            'message' => 'بيانات الاعتماد غير صحيحة.'
        ], 401);
    }

    // حذف التوكنات القديمة (اختياري)
    $user->tokens()->delete();

    // إنشاء توكن جديد
    $token = $user->createToken('api_token')->plainTextToken;

    return response()->json([
        'user' => $user,
        'access_token' => $token,
        'token_type'   => 'Bearer',
    ], 200);
}


    /**
     * Handle user logout.
     */
   public function logout(Request $request)
    {
        // احذف التوكن الجاري
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج.'
        ]);
    }
}

