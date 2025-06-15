<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectFormRequest;
use App\Http\Requests\UpdateProjectFormRequest;
use App\Http\Requests\UpdateProjectFormStatusRequest;
use App\Http\Resources\ProjectFormCollection;
use App\Http\Resources\ProjectFormResource;
use App\Models\ProjectForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProjectFormController extends Controller
{
public function __construct()
{
    $this->middleware('auth:sanctum');
    
    // فعّل التفويض على الـ Resource
$this->authorizeResource(\App\Models\ProjectForm::class, 'project_form');
}



    /**
     * عرض نماذج المشاريع paginated للمستخدم الجاري
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);

        $projects = ProjectForm::with('user')
            ->where('user_id', $request->user()->id)
            ->orderBy('submitted_at', 'desc')
            ->paginate($perPage);

        return new ProjectFormCollection($projects);
    }

    /**
     * عرض نموذج مشروع واحد
     */
    public function show(ProjectForm $project_form)
    {
        return new ProjectFormResource(
            $project_form->load('user')
        );
    }

    /**
     * إنشاء نموذج مشروع جديد
     */
   public function store(Request $request)
{
    $data = $request->validate([
        'title'       => 'required|string',
        'description' => 'required|string',
        'pdf'         => 'required|file|mimes:pdf',
        'supervisor'  => 'required|string|max:255',  // ← حقل مطلوب
    ]);

    // رفع الملف
    $pdfPath = $request->file('pdf')->store('projects', 'public');

    // إنشاء المشروع مع السوبرفايزور
    $project = ProjectForm::create([
        'title'       => $data['title'],
        'description' => $data['description'],
        'pdf_path'    => $pdfPath,
        'user_id'     => auth()->id(),
        'supervisor'  => $data['supervisor'],       // ← تمريره هنا
    ]);

    return response()->json([
        'message' => 'تم إنشاء المشروع بنجاح.',
        'project' => $project,
    ], 201);
}

    /**
     * تحديث نموذج مشروع موجود
     */
    public function update(UpdateProjectFormRequest $request, ProjectForm $project_form)
    {
        $data = $request->validated();

        if ($request->hasFile('pdf')) {
            DB::transaction(function () use ($project_form, $request, &$data) {
                // إحذف القديم وخزن الجديد
                Storage::disk('public')->delete($project_form->pdf_path);
                $data['pdf_path'] = $request->file('pdf')->store('project_pdfs', 'public');
                $project_form->update($data);
            });
        } else {
            $project_form->update($data);
        }

        return new ProjectFormResource(
            $project_form->fresh('user')
        );
    }

    /**
     * حذف نموذج مشروع
     */
    public function destroy(ProjectForm $project_form)
    {
        DB::transaction(function () use ($project_form) {
            Storage::disk('public')->delete($project_form->pdf_path);
            $project_form->delete();
        });

        return response()->noContent();
    }

    /**
     * تحديث حالة المشروع (للمديرين فقط)
     */
    public function updateStatus(Request $request, ProjectForm $project_form)
{
    $data = $request->validate([
        'status' => 'required|string'
    ]);

    // نحديث الحالة
    $project_form->status = $data['status'];
    $project_form->save();

    return response()->json([
        'message' => 'Status updated',
        'project' => $project_form
    ]);
}

    /**
     * إحصائيات ومشاريع المستخدم
     */
    public function myProjects(Request $request)
    {
        $projects = $request->user()
            ->projectForms()
            ->orderBy('submitted_at', 'desc')
            ->get();

        return response()->json([
            'user'           => $request->user()->only('id', 'name'),
            'projects_count' => $projects->count(),
            'projects'       => ProjectFormResource::collection($projects),
        ], 200);
    }
}
