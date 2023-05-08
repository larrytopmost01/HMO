<?php
namespace App\Http\Controllers\Api\V1;
use Illuminate\Http\Request;
use App\Utils\ResponseFormatter;
use App\Models\AppVersion;

class AppVersionController extends Controller
{
    public function getAppVersion()
    {
        $app_versions = AppVersion::first();
        $data = ['minor' => $app_versions->minor, 'major' => $app_versions->major];
        return response()->json(ResponseFormatter::successResponse('app versions retrived successfully', $data), 200);
    }
}
