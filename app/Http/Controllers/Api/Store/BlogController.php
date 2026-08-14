<?php

namespace App\Http\Controllers\Api\Store;

use App\Http\Controllers\Api\ApiBaseController;
use App\Services\Api\Store\BlogApiService;
use Illuminate\Http\Request;

final class BlogController extends ApiBaseController
{
    public function __construct(
        private readonly BlogApiService $blogService,
    ) {
        parent::__construct(app(\App\Http\Api\Response\Builder\ApiResponseBuilder::class));
    }

    public function index(Request $request)
    {
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);
        $result = $this->blogService->list($page, $perPage);

        return $this->respondSuccess(
            $result,
            'Blog posts retrieved successfully'
        );
    }

    public function show(string $slug)
    {
        return $this->respondSuccess(
            $this->blogService->findBySlug($slug),
            'Blog post retrieved successfully'
        );
    }
}
