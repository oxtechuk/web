<?php

namespace App\Http\Controllers\Api\Store;

use App\Http\Controllers\Api\ApiBaseController;
use App\Services\Api\Store\SettingApiService;
use Illuminate\Http\Request;

final class SettingsController extends ApiBaseController
{
    public function __construct(
        private readonly SettingApiService $settingService,
    ) {
        parent::__construct(app(\App\Http\Api\Response\Builder\ApiResponseBuilder::class));
    }

    public function index(Request $request)
    {
        $keys = $request->query('keys');

        if (is_string($keys)) {
            $keys = explode(',', $keys);
        }

        $keys = is_array($keys) ? $keys : null;

        return $this->respondSuccess(
            $this->settingService->list($keys),
            'Settings retrieved successfully'
        );
    }
}
