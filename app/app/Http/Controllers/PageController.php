<?php

namespace App\Http\Controllers;

use App\Helpers\WebsiteHelper;
use App\Models\Page;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Pipeline;

class PageController extends Controller
{
    use ApiTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = Pipeline::send(Page::query()->latest())
            ->through([])
            ->thenReturn();

        return $this->sendResponse('Data successfully retrieved', data: $model->get());
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required',
        ]);

        // Extract title
        $metadata = WebsiteHelper::extractWebsiteMetadata($request->url);

        Page::create([
            'title' => $metadata['title'],
            'image' => $metadata['image'],
            'description' => $metadata['description'],
            'url' => $request->url,
            'user_id' => Auth::id(),
        ]);

        return $this->sendResponse('Data successfully created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Page $page)
    {
        return $this->sendResponse('Data successfully retrieved', data: $page);
    }

    /**
     * Update the specified resource in storage.
     */
    public function favorite(Page $page)
    {
        $page->is_favorite = $page->is_favorite == 1 ? 0 : 1;
        $page->save();

        return $this->sendResponse('Data successfully update');
    }

    public function read(Page $page)
    {
        $page->is_read = $page->is_read == 1 ? 0 : 1;
        $page->save();

        return $this->sendResponse('Data successfully update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        $page->delete();
        return $this->sendResponse('Data successfully deleted');
    }
}
