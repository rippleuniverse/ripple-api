<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Blog\AssetResource;
use App\Http\Resources\Blog\BlogItemResource;
use App\Http\Resources\Blog\CategoryResource;
use App\Models\Blog;
use App\Models\BlogAssets;
use App\Models\BlogCategory;
use App\Traits\Files;
use App\Traits\HttpResponses;
use App\Traits\Pagination;
use Illuminate\Http\Request;

class BlogsController extends Controller
{
    use HttpResponses, Files, Pagination;

    public function viewAllCategories()
    {
        $categories = BlogCategory::all();
        $data = CategoryResource::collection($categories);

        return $this->success($data);
    }

    public function viewAll()
    {
        $blogs = Blog::filter()->latest()->paginate(12);
        $list = BlogItemResource::collection($blogs);
        $data = $this->paginatedData($blogs, $list);

        return $this->success($data);
    }

    public function viewRelatedBlogs(string $slug)
    {
        $blog = Blog::where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        $blogs = Blog::where('blog_category_id', $blog->blog_category_id)
            ->where('id', '!=', $blog->id)
            ->latest()
            ->take(4)
            ->get();
        $list = BlogItemResource::collection($blogs);

        return $this->success($list);
    }

    public function view(string $slug)
    {
        $blog = Blog::where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        $nextBlog = Blog::where('id', '>', $blog->id)->first();
        $prevBlog = Blog::where('id', '<', $blog->id)->latest()->first();
        $data = [
            'id' => (string)$blog->id,
            'title' => $blog->title,
            'slug' => $blog->slug,
            'description' => $blog->description,
            'content' => $blog->content,
            'featured_image' => $this->getFilePath($blog->featured_image),
            'created_at' => $blog->created_at->format('M d, Y'),
            'read_time' => $blog->read_time,
            'author' => $blog->author,
            'category' => [
                'id' => (string)$blog->blog_category_id,
                'name' => $blog->category->name
            ],
            'next_blog' => $nextBlog?->slug,
            'prev_blog' => $prevBlog?->slug,
        ];

        return $this->success($data);
    }

    public function uploadAsset(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,gif', 'max:10240'],
        ]);

        $file = $this->uploadFile($request->file('file'), 'blog_assets');

        BlogAssets::create(['file' => $file]);

        return $this->success([
            'url' => $this->getFilePath($file),
        ]);

    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:191'],
            'slug' => ['required', 'string', 'max:191', 'unique:blogs,slug'],
            'blog_category_id' => ['required', 'exists:blog_categories,id'],
            'description' => ['required', 'string'],
            'featured_image' => ['required', 'mimes:jpg,jpeg,png', 'max:5120'],
            'content' => ['required', 'string'],
            'author' => ['required', 'string'],
        ]);

        $featuredImage = $this->uploadFile($request->file('featured_image'), 'blog/featured-images');
        $data['featured_image'] = $featuredImage;
        Blog::create($data);
        return $this->success(null, 'Blog created successfully.');
    }

    public function update(Blog $blog, Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:191'],
            'slug' => ['required', 'string', 'max:191', 'unique:blogs,slug,' . $blog->id],
            'blog_category_id' => ['required', 'exists:blog_categories,id'],
            'description' => ['required', 'string'],
            'featured_image' => ['nullable', 'mimes:jpg,jpeg,png', 'max:5120'],
            'content' => ['required', 'string'],
            'author' => ['required', 'string'],
        ]);
        $featuredImage = $request->file('featured_image') ?
            $this->uploadFile($request->file('featured_image'), 'blog/featured-images')
            : $blog->featured_image;

        if ($featuredImage != $blog->featured_image) {
            $this->deleteFile($blog->featured_image);
        }

        $data['featured_image'] = $featuredImage;
        $blog->update($data);
        return $this->success(null, 'Blog updated successfully.');
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
        return $this->success(null, 'Blog deleted successfully.');
    }


    public function listAssets()
    {
        $assets = BlogAssets::latest()->paginate(12);
        $list = AssetResource::collection($assets);
        $data = $this->paginatedData($assets, $list);

        return $this->success($data);
    }

    public function destroyAsset(BlogAssets $asset)
    {

        $this->deleteFile($asset->file);
        $asset->delete();

        return $this->success(null, 'Asset deleted successfully.');
    }


}
