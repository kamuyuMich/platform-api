<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SiteProfileResource;
use App\Models\SiteProfile;

class SiteProfileController extends Controller
{
    /**
     * GET /api/profile
     */
    public function show()
    {
        return new SiteProfileResource(SiteProfile::current());
    }
}
