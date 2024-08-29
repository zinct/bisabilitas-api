<?php

namespace App\Http\Controllers;

use App\Helpers\WebsiteHelper;
use App\Services\RecommendationService;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Http\Request;

class RecommendationPageController extends Controller
{
    use ApiTrait;

    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    public function index()
    {
        // try {
        $recommendations = $this->recommendationService->getRecommendations(5);
        return $this->sendResponse('Data successfully retrieved', data: $recommendations);
        // } catch (Exception $err) {
        //     return $this->sendResponse('Data successfully retrieved', data: [], showDataWhenNull: true);
        // }
    }
}
