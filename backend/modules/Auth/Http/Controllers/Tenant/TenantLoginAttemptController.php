<?php

namespace Modules\Auth\Http\Controllers\Tenant;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Modules\Auth\Entities\LoginAttempt;
class TenantLoginAttemptController extends ApiController
{
    /**
     * Display login attempts for current user or specific user
     */
    public function index(Request $request, int $id = null)
    {
        $userId = $id ?? auth()->id();
        $route = route('tenant.login-attempts.index', $userId);

        $title = translate('login_attempts');
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate('login_attempts')],
        ];

        return view('tenant.auth.login-attempts.index', compact('route', 'title', 'breadcrumbs'));
    }

    private function formatAgentIcons($agent)
    {
        $icons = '';

        // OS Detection
        if (stripos($agent, 'Linux') !== false) {
            $icons .= '<i class="fab fa-linux" title="Linux"></i> ';
        } elseif (stripos($agent, 'Windows') !== false) {
            $icons .= '<i class="fab fa-windows" title="Windows"></i> ';
        } elseif (stripos($agent, 'Mac') !== false) {
            $icons .= '<i class="fab fa-apple" title="MacOS"></i> ';
        } elseif (stripos($agent, 'Android') !== false) {
            $icons .= '<i class="fab fa-android" title="Android"></i> ';
        } elseif (stripos($agent, 'iPhone') !== false || stripos($agent, 'iPad') !== false) {
            $icons .= '<i class="fab fa-apple" title="iOS"></i> ';
        }

        // Browser Detection
        if (stripos($agent, 'Chrome') !== false) {
            $icons .= '<i class="fab fa-chrome" title="Chrome"></i>';
        } elseif (stripos($agent, 'Firefox') !== false) {
            $icons .= '<i class="fab fa-firefox-browser" title="Firefox"></i>';
        } elseif (stripos($agent, 'Safari') !== false) {
            $icons .= '<i class="fab fa-safari" title="Safari"></i>';
        } elseif (stripos($agent, 'Edge') !== false) {
            $icons .= '<i class="fab fa-edge" title="Edge"></i>';
        } elseif (stripos($agent, 'Opera') !== false) {
            $icons .= '<i class="fab fa-opera" title="Opera"></i>';
        }

        return $icons ?: '<span><i class="fa-solid fa-globe" title="' . $agent . '"></i><span>' . translate("unknown_browser") . '</span>';
    }
}

