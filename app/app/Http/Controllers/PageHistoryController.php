<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;

class PageHistoryController extends Controller
{
    use ApiTrait;

    public function index()
    {
        $model = Page::orderBy('updated_at', 'DESC');
        return $this->sendResponse('Data successfully retrieved', data: $model->get());
    }

    public function read(Page $page)
    {
        $page->touch();
        return $this->sendResponse('Success updated');
    }
}
