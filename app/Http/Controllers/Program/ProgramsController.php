<?php

namespace App\Http\Controllers\Program;

use App\Http\Controllers\Controller;
use App\Http\Resources\Program\CategoryResource;
use App\Http\Resources\Program\ProgramResource;
use App\Http\Resources\Program\RatingResource;
use App\Models\Program;
use App\Models\ProgramCategory;
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
            'description' => $program->description,
            'author' => $program->author,
            'skills' => explode(',', $program->skills),
            'experience_level' => $program->experience_level,
            'category' => [
                'id' => (string)$program->program_category_id,
                'name' => $program->category->name,
                'slug' => $program->category->slug,
            ],
            'price' => sanitizedJsonDecode($program->price, true),
            'rating' => [
                'avg_rating' => $program->ratings()->avg('rating') ?? 0,
                'count' => $program->ratings()->count()
            ],
            'featured_image' => $this->getFilePath($program->featured_image),
            'created_at' => $program->created_at->format('Y-m-d'),
        ];

        return $this->success($data);
    }

    public function viewCategories()
    {
        $categories = ProgramCategory::all();
        $list = CategoryResource::collection($categories);

        return $this->success($list);
    }

    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'category_id' => ['required', 'exists:program_categories,id'],
                'name' => ['required', 'string', 'max:255'],
                'author' => ['required', 'string', 'max:255'],
                'experience_level' => ['required', 'string', 'in:beginner,intermediate,expert'],
                'skills' => ['required', 'array', 'min:1'],
                'description' => ['required', 'string'],
                'skills.*' => ['required', 'string', 'max:255'],
                //                Max of 5mb
                'featured_image' => ['required', 'mimes:png,jpeg,jpg', 'max:5120'],
                'file' => ['required', 'mimes:pdf,docx,doc,zip,rar'],
                'price' => ['array', 'min:2', 'max:2'],
                'price.0.currency' => ['required', 'string', 'in:NGN'],
                'price.0.amount' => ['required', 'numeric:', 'min:0'],
                'price.1.currency' => ['required', 'string', 'in:USD'],
                'price.1.amount' => ['required', 'numeric:', 'min:0'],
            ]
        );

        $featuredImage = $this->uploadFile($request->file('featured_image'), 'programs');
        $data['featured_image'] = $featuredImage;
        $data['skills'] = implode(',', $data['skills']);
        $data['file'] = $this->uploadFile($request->file('file'), 'programs', 'private');
        $data['price'] = json_encode($data['price']);
        Program::create([
            'program_category_id' => $data['category_id'],
            ...$data
        ]);


        return $this->success(null, 'Program created successfully.');

    }

    public function reviews(Program $program)
    {
        $ratings = $program->ratings()->latest()->paginate(10);
        $list = RatingResource::collection($ratings);
        $data = $this->paginatedData($ratings, $list);
        return $this->success($data);
    }

    public function viewRelated(Program $program)
    {
        $programs = Program::where('program_category_id', $program->program_category_id)
            ->where('id', '!=', $program->id)
            ->latest()
            ->take(8)
            ->get();
        $list = ProgramResource::collection($programs);

        return $this->success($list);
    }

    public function update(Program $program, Request $request)
    {
        $data = $request->validate(
            [
                'category_id' => ['required', 'exists:program_categories,id'],
                'name' => ['required', 'string', 'max:255'],
                'author' => ['required', 'string', 'max:255'],
                'experience_level' => ['required', 'string', 'in:beginner,intermediate,expert'],
                'skills' => ['required', 'array', 'min:1'],
                'skills.*' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                //                Max of 5mb
                'featured_image' => ['nullable', 'mimes:png,jpeg,jpg', 'max:5120'],
                'file' => ['nullable', 'mimes:pdf,docx,doc,zip,rar'],
                'price' => ['array', 'min:2', 'max:2'],
                'price.0.currency' => ['required', 'string', 'in:NGN'],
                'price.0.amount' => ['required', 'numeric:', 'min:0'],
                'price.1.currency' => ['required', 'string', 'in:USD'],
                'price.1.amount' => ['required', 'numeric:', 'min:0'],
            ]
        );

        $featuredImage = $request->file('featured_image') ? $this->uploadFile($request->file('featured_image'), 'programs') : $program->featured_image;
        $data['featured_image'] = $featuredImage;
        $data['skills'] = implode(',', $data['skills']);
        $data['file'] = $request->file('file') ?
            $this->uploadFile($request->file('file'), 'programs', 'private') : $program->file;
        $data['price'] = json_encode($data['price']);
        $program->update($data);

        return $this->success(null, 'Program updated successfully.');
    }

    public function destroy(Program $program)
    {
        $this->deleteFile($program->featured_image);
        $program->delete();
        return $this->success(null, 'Program deleted successfully.');
    }
}
