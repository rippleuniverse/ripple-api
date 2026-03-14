<?php

namespace App\Http\Controllers\OpenRoles;

use App\Http\Controllers\Controller;
use App\Http\Resources\OpenRole\RoleListItemResource;
use App\Http\Resources\OpenRole\RoleResource;
use App\Models\OpenRole;
use App\Traits\Pagination;
use Illuminate\Http\Request;

class OpenRolesController extends Controller
{

    use Pagination;

    public function viewAll()
    {
        $roles = OpenRole::filter()->latest()->paginate(12);
        $list = RoleResource::collection($roles);
        $data = $this->paginatedData($roles, $list);

        return $this->success($data);
    }

    public function overview()
    {
        $roles = OpenRole::latest()->take(8)->get();
        return $this->success(RoleResource::collection($roles));
    }

    public function homeOverview()
    {
        $roles = OpenRole::latest()->take(3)->get();
        return $this->success(RoleResource::collection($roles));
    }

    public function viewAllWithoutPagination()
    {
        $roles = OpenRole::all();
        return $this->success(RoleListItemResource::collection($roles));
    }

    public function view(OpenRole $role)
    {
        $data = new RoleResource($role);
        return $this->success($data);
    }

    public function store(Request $request)
    {
        $data = $this->roleValidation($request);

        OpenRole::create($data);

        return $this->success(null, 'Role created successfully.');
    }

    public function update(OpenRole $role, Request $request)
    {
        $data = $this->roleValidation($request);

        $role->update($data);

        return $this->success(null, 'Role updated successfully.');
    }

    public function destroy(OpenRole $role)
    {
        $role->delete();

        return $this->success(null, 'Role deleted successfully.');
    }

    /**
     * @param Request $request
     * @return array
     */
    public function roleValidation(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'company_name' => ['required', 'string', 'max:191'],
            'company_location' => ['required', 'string', 'max:191'],
            'type' => ['required', 'in:full_time,part_time,internship,contract'],
            'experience_level' => ['required', 'in:beginner,intermediate,expert'],
            'style' => ['required', 'in:remote,on_site,hybrid'],
            'salary' => ['required', 'string'],
            'description' => ['required', 'string'],
            'about_company' => ['required', 'string'],
            'responsibilities' => ['required', 'array', 'min:1'],
            'responsibilities.*' => ['required', 'string'],
            'requirements' => ['required', 'array', 'min:1'],
            'requirements.*' => ['required', 'string'],
            'benefits' => ['required', 'array', 'min:1'],
            'benefits.*' => ['required', 'string'],
        ]);
        return $data;
    }
}
