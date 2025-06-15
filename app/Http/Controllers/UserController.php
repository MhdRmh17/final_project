<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateProfileRequest;


class UserController extends Controller
{
    // Apply Sanctum auth middleware
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display the specified user along with profile fields.
     * GET /api/users/{id}
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return new UserResource($user);
    }

    /**
     * Update the specified user's profile fields.
     * PUT/PATCH /api/users/{id}
     */
public function updateProfile(Request $request)
    {
        // 1) أقلّ قواعد validate مباشرة هنا
        $data = $request->validate([
            'name'      => 'sometimes|required|string|max:255',
            'phone'     => 'sometimes|nullable|string|max:20',
            'birthdate' => 'sometimes|nullable|date',
            'address'   => 'sometimes|nullable|string|max:255',
        ]);

        // 2) حدّث المستخدم المصادق عليه
        $user = $request->user(); 
        $user->update($data);

        // 3) أرجع الموارد (Resource) بتنظيم جميل
        return new UserResource($user);
    }

}
