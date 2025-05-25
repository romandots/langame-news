<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchNewsRequest;
use App\Services\News\NewsService;
use Illuminate\Http\JsonResponse;

class NewsController extends Controller
{
    public function index()
    {
        return view('news.index');
    }

    public function fetch(NewsService $service, SearchNewsRequest $request): JsonResponse
    {
        $searchNews = $request->getDto();
        $newsResponse = $service->search($searchNews);
        return response()->json($newsResponse);
    }
}
