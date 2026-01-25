<?php

namespace App\Http\Controllers\Program;

use App\Http\Controllers\Controller;
use App\Http\Resources\Program\ModuleResource;
use App\Http\Resources\Program\ProgramResource;
use App\Models\Program;
use App\Traits\Files;
use App\Traits\Pagination;
use Illuminate\Http\Request;

class ProgramsController extends Controller
{
    use Files, Pagination;


    public function viewAll()
    {
        $programs = Program::filter()->latest()->paginate(12);
        $list = ProgramResource::collection($programs);

        $data = $this->paginatedData($programs, $list);

        return $this->success($data);
    }

    public function view(Program $program)
    {
        $data = [
            'id' => $program->id,
            'name' => $program->name,
            'author' => $program->author,
            'skills' => explode(',', $program->skills),
            'experience_level' => $program->experience_level,
            'category' => [
                'id' => (string)$program->program_category_id,
                'name' => $program->category->name,
                'slug' => $program->category->slug,
            ],
            'formatted_price' => currencyFormat($program->price),
            'price' => (float)$program->price,
            'rating' => [
                'avg_rating' => $program->ratings()->avg('rating'),
                'count' => $program->ratings()->count()
            ],
            'featured_image' => $this->getFilePath($program->featured_image),
            'created_at' => $program->created_at->format('Y-m-d'),
            'modules' => ModuleResource::collection($program->modules),
        ];

        return $this->success($data);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
                'category_id' => ['required', 'exists:program_categories,id'],
                'name' => ['required', 'string', 'max:255'],
                'author' => ['required', 'string', 'max:255'],
                'experience_level' => ['required', 'string', 'in:beginner,intermediate,expert'],
                'price' => ['required', 'numeric', 'min:0'],
                'skills' => ['required', 'array', 'min:1'],
                'skills.*' => ['required', 'string', 'max:255'],
//                Max of 5mb
                'featured_image' => ['required', 'mimes:png,jpeg,jpg', 'max:5120'],
                'modules' => ['required', 'array', 'min:1'],
                'modules.*.module_no' => ['required', 'numeric'],
                'modules.*.title' => ['required', 'string', 'max:255'],
                'modules.*.description' => ['required', 'nullable', 'string'],
            ]
        );

        $featuredImage = $this->uploadFile($request->file('featured_image'), 'programs');
        $data['featured_image'] = $featuredImage;
        $data['skills'] = implode(',', $data['skills']);
        $program = Program::create([
            'program_category_id' => $data['category_id'],
            ...$data
        ]);

        $program->modules()->createMany($data['modules']);

        return $this->success(null, 'Program created successfully.');

    }

    public function update(Program $program, Request $request)
    {
        $data = $request->validate([
                'category_id' => ['required', 'exists:program_categories,id'],
                'name' => ['required', 'string', 'max:255'],
                'author' => ['required', 'string', 'max:255'],
                'experience_level' => ['required', 'string', 'in:beginner,intermediate,expert'],
                'price' => ['required', 'numeric', 'min:0'],
//                Max of 5mb
                'featured_image' => ['nullable', 'mimes:png,jpeg,jpg', 'max:5120'],
                'modules' => ['required', 'array', 'min:1'],
                'modules.*.module_no' => ['required', 'numeric'],
                'modules.*.id' => ['nullable', 'string'],
                'modules.*.title' => ['required', 'string', 'max:255'],
                'modules.*.description' => ['required', 'nullable', 'string'],
            ]
        );

        $featuredImage = $request->file('featured_image') ? $this->uploadFile($request->file('featured_image'), 'programs') : $program->featured_image;
        $data['featured_image'] = $featuredImage;
        $data['skills'] = implode(',', $data['skills']);
        $program->update($data);

        $modules = $data['modules'];
        foreach ($modules as $module) {
            $program->modules()->updateOrCreate(['id' => $module['id']], [
                'title' => $module['title'],
                'description' => $module['description']
            ]);
        }

        return $this->success(null, 'Program updated successfully.');
    }

    public function destroy(Program $program)
    {
        $this->deleteFile($program->featured_image);
        $program->delete();
        return $this->success(null, 'Program deleted successfully.');
    }
}
