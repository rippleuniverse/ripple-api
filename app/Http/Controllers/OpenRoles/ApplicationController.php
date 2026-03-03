<?php

namespace App\Http\Controllers\OpenRoles;

use App\Enums\StatusCode;
use App\Http\Controllers\Controller;
use App\Http\Resources\OpenRole\ApplicationResource;
use App\Mail\OpenRoles\ApplicationMail;
use App\Models\JobApplication;
use App\Traits\Files;
use App\Traits\Pagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ApplicationController extends Controller
{
    use Files, Pagination;

    public function store(Request $request)
    {
        $data = $request->validate([
            'open_role_id' => ['required', 'exists:open_roles,id'],
            'first_name' => ['required', 'string', 'max:191'],
            'last_name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string'],
//            Max of 5mb
            'cv' => ['required', 'mimes:pdf,docx,doc', 'max:5120'],
            'personal_url' => ['nullable', 'url'],
        ]);
        try {
            DB::transaction(function () use ($data, $request) {
                $hasApplied = JobApplication::where('open_role_id', $data['open_role_id'])
                    ->where('email', $data['email'])->exists();

                if ($hasApplied) {
                    return $this->failed(null, StatusCode::Forbidden->value, 'You have already applied for this role.');
                }

                $cv = $this->uploadFile($request->file('cv'), 'applications/cvs');
                $data['cv'] = $cv;
                $application = JobApplication::create($data);

                Mail::to($data['email'])->send(new ApplicationMail($application));
            });

            return $this->success(null, 'Application created successfully.');
        } catch (\Throwable|\Error $th) {
            return $this->failed(null, StatusCode::InternalServerError->value, 'Something went wrong.');
        }


    }

    public function viewAll()
    {
        $applications = JobApplication::filter()->latest()->paginate(12);
        $list = ApplicationResource::collection($applications);
        $data = $this->paginatedData($applications, $list);

        return $this->success($data);
    }

    public function view(JobApplication $application)
    {
        $data = new ApplicationResource($application);
        return $this->success($data);
    }

    public function destroy(JobApplication $application)
    {
        $application->delete();
        return $this->success(null, 'Application deleted successfully.');
    }
}
