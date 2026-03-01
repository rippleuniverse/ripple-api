<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Blog\AssetResource;
use App\Models\BlogAssets;
use App\Traits\Files;
use App\Traits\HttpResponses;
use App\Traits\Pagination;
use Illuminate\Http\Request;

class BlogsController extends Controller
{
    use HttpResponses, Files, Pagination;

    public function uploadAsset(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,gif'],
        ]);

        $file = $this->uploadFile($request->file('file'), 'blog_assets');

        BlogAssets::create(['file' => $file]);

        return $this->success([
            'url' => $this->getFilePath($file),
        ]);

    }


    public function listAssets(Request $request)
    {
        $assets = BlogAssets::latest()->paginate(12);
        $list = AssetResource::collection($assets);
        $data = $this->paginatedData($assets, $list);

        return $this->success($data);
    }


}
