<?php

namespace App\Http\Controllers;

use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    use ApiTrait;

    public function index()
    {
        $user = Auth::user();
        return $this->sendResponse('Profile successfully', data: $user);
    }
}
