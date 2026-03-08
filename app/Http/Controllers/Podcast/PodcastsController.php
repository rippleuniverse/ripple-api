<?php

namespace App\Http\Controllers\Podcast;

use App\Enums\StatusCode;
use App\Http\Controllers\Controller;
use App\Http\Resources\Podcast\CategoryResource;
use App\Http\Resources\Podcast\PodcastResource;
use App\Models\Podcast;
use App\Models\PodcastCategory;
use App\Traits\Files;
use App\Traits\Pagination;
use Illuminate\Http\Request;

class PodcastsController extends Controller
{
    use Files, Pagination;

    public function viewAll()
    {
        $podcasts = Podcast::filter()->latest()->paginate(12);
        $list = PodcastResource::collection($podcasts);
        $data = $this->paginatedData($podcasts, $list);

        return $this->success($data);
    }

    public function overview()
    {
        $podcasts = Podcast::latest()->take(2)->get();
        $data = PodcastResource::collection($podcasts);

        return $this->success($data);
    }

    public function viewCategories()
    {
        $categories = PodcastCategory::all();
        $list = CategoryResource::collection($categories);

        return $this->success($list);
    }

    public function viewRelatedPodcasts(Podcast $podcast)
    {
        $podcasts = Podcast::where('podcast_category_id', $podcast->podcast_category_id)
            ->where('id', '!=', $podcast->id)
            ->latest()
            ->take(4)
            ->get();
        $list = PodcastResource::collection($podcasts);

        return $this->success($list);
    }

    public function view(Podcast $podcast)
    {
        $data = new PodcastResource($podcast);
        return $this->success($data);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'podcast_category_id' => ['required', 'exists:podcast_categories,id'],
            'title' => ['required', 'string', 'max:191'],
            'description' => ['required', 'string'],
            'audio' => ['required', 'mimes:mp3,wav,flac'],
            'duration_in_minutes' => ['required', 'numeric', 'min:0'],
            'featured_image' => ['required', 'mimes:jpg,jpeg,png', 'max:5120']
        ]);

        $featuredImage = $this->uploadFile($request->file('featured_image'), 'podcasts/featured-images');
        $audio = $this->uploadFile($request->file('audio'), 'podcasts/audio');
        $data['featured_image'] = $featuredImage;
        $data['audio'] = $audio;
        Podcast::create($data);

        return $this->failed(null, StatusCode::Success->value, 'Podcast created successfully.');
    }

    public function update(Request $request, Podcast $podcast)
    {
        $data = $request->validate([
            'podcast_category_id' => ['required', 'exists:podcast_categories,id'],
            'title' => ['required', 'string', 'max:191'],
            'description' => ['required', 'string'],
            'audio' => ['nullable', 'mimes:mp3,wav,flac'],
            'duration_in_minutes' => ['required', 'numeric', 'min:0'],
            'featured_image' => ['nullable', 'mimes:jpg,jpeg,png', 'max:5120']
        ]);

        $featuredImage = $request->file('featured_image') ?
            $this->uploadFile($request->file('featured_image'), 'podcasts/featured-images')
            : $podcast->featured_image;
        $audio = $request->file('audio') ?
            $this->uploadFile($request->file('audio'), 'podcasts/audio') :
            $podcast->audio;

        $data['featured_image'] = $featuredImage;
        $data['audio'] = $audio;
        $podcast->update($data);

        return $this->success(null, 'Podcast updated successfully.');
    }

    public function destroy(Podcast $podcast)
    {
        $this->deleteFile($podcast->featured_image);
        $this->deleteFile($podcast->audio);
        $podcast->delete();

        return $this->success(null, 'Podcast deleted successfully.');
    }
}
