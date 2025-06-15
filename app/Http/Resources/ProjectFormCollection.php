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
        $this->authorizeResource(ProjectForm::class, 'project_form');
    }

    /** عرض قائمة المشاريع بصفحة مُرتَّبة ومجزّأة */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $projects = ProjectForm::with('user')
                       ->where('user_id', $request->user()->id)
                       ->orderBy('submitted_at', 'desc')
                       ->paginate($perPage);

        return new ProjectFormCollection($projects);
    }

    /** عرض نموذج مشروع واحد */
    public function show(ProjectForm $project_form)
    {
        return new ProjectFormResource($project_form->load('user'));
    }

    /** إنشاء نموذج مشروع جديد */
    public function store(StoreProjectFormRequest $request)
    {
        $path = $request->file('pdf')->store('project_pdfs', 'public');

        $form = $request->user()
                        ->projectForms()
                        ->create(array_merge(
                            $request->validated(),
                            ['pdf_path' => $path]
                        ));

        return new ProjectFormResource($form->load('user'));
    }

    /** تحديث نموذج المشروع */
    public function update(UpdateProjectFormRequest $request, ProjectForm $project_form)
    {
        $data = $request->validated();

        if ($request->hasFile('pdf')) {
            DB::transaction(function() use ($project_form, $request, &$data) {
                Storage::disk('public')->delete($project_form->pdf_path);
                $data['pdf_path'] = $request->file('pdf')->store('project_pdfs', 'public');
                $project_form->update($data);
            });
        } else {
            $project_form->update($data);
        }

        return new ProjectFormResource($project_form->fresh('user'));
    }

    /** حذف نموذج المشروع */
    public function destroy(ProjectForm $project_form)
    {
        DB::transaction(function() use ($project_form) {
            Storage::disk('public')->delete($project_form->pdf_path);
            $project_form->delete();
        });

        return response()->noContent();
    }

    /** تحديث حالة المشروع (للمديرين فقط) */
    public function updateStatus(UpdateProjectFormStatusRequest $request, ProjectForm $project_form)
    {
        $project_form->update($request->validated());
        return response()->json([
            'message' => 'Status updated successfully.',
            'project' => new ProjectFormResource($project_form->fresh('user')),
        ]);
    }

    /** عرض إحصائيات مشاريعي */
    public function myProjects(Request $request)
    {
        $projects = $request->user()
                            ->projectForms()
                            ->orderBy('submitted_at', 'desc');

        return response()->json([
            'user'           => $request->user()->only('id','name'),
            'projects_count' => $projects->count(),
            'projects'       => ProjectFormResource::collection($projects->get()),
        ]);
    }
}
