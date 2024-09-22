<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageHistoryController extends Controller
{
    use ApiTrait;

    public function index()
    {
        $model = Page::where('user_id', Auth::id())->orderBy('updated_at', 'DESC');
        return $this->sendResponse('Data successfully retrieved', data: $model->get());
    }

    public function read(Page $page)
    {
        $page->touch();
        return $this->sendResponse('Success updated');
    }
}
