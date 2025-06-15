<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProjectForm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;    // ← استيراد النوع الصحيح

class DashboardController extends Controller
{
public function index(): JsonResponse
    {
        // Projects ما بتتغير
        $projects = ProjectForm::with('user')->get();

        // بدل Profile، جلب من users مباشرة
        $users = User::select([
                'id',
                'name',
                'email',
                'phone',
                'birthdate',
                'address',
                'type',
                'registered_at',
                'created_at',
                'updated_at',
            ])->get();

        return response()->json([
            'projects' => $projects,
            'users'    => $users,
        ], 200);
    }
public function updateStatus(Request $request, ProjectForm $project_form)
{
    // تحقق من أن الحقل status موجود في البيانات المرسلة
    $data = $request->validate([
        'status' => 'required|string|in:pending,accepted,rejected',
    ]);

    // تحديث الحالة
    $project_form->status = $data['status'];
    $project_form->save();

    return response()->json([
        'message' => 'تم تحديث الحالة بنجاح.',
        'project' => $project_form,
    ]);
}

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required'
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'تم تحديث كلمة المرور بنجاح.'
        ]);
    }
}
