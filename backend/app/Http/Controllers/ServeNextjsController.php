<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Modules\Tenant\Helper\TenantHelper;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ServeNextjsController extends Controller
{
    /**
     * Serve the appropriate Next.js static export based on the request's domain.
     *
     * - landlord.saas.test → landlord-frontend/out/
     * - *.saas.test (tenant) → tenant-frontend/out/
     *
     * Static assets under /landlord-assets/ and /tenant-assets/ are served
     * directly via symlinks in public/; this controller handles HTML pages only.
     */
    public function __invoke(Request $request)
    {
        $subdomain = TenantHelper::getSubDomain();
        $buildDir = $subdomain === 'landlord'
            ? base_path('../landlord-frontend/out')
            : base_path('../tenant-frontend/out');

        // Normalise the path: strip leading slash, treat empty as index
        $path = trim($request->path(), '/');

        // Never intercept API routes
        if (str_starts_with($path, 'api/')) {
            throw new NotFoundHttpException();
        }

        // Try exact file, then index.html inside the path directory, then root index.html
        $candidates = [
            $buildDir . '/' . $path . '.html',
            $buildDir . '/' . $path . '/index.html',
            $buildDir . '/index.html',
        ];

        // If path is empty, just serve root
        if ($path === '' || $path === '/') {
            $candidates = [$buildDir . '/index.html'];
        }

        foreach ($candidates as $file) {
            if (File::exists($file)) {
                return response(File::get($file), 200, [
                    'Content-Type' => 'text/html; charset=utf-8',
                    'Cache-Control' => 'no-cache, must-revalidate',
                ]);
            }
        }

        // Fallback: serve root index.html for SPA client-side routing
        $fallback = $buildDir . '/index.html';
        if (File::exists($fallback)) {
            return response(File::get($fallback), 200, [
                'Content-Type' => 'text/html; charset=utf-8',
                'Cache-Control' => 'no-cache, must-revalidate',
            ]);
        }

        throw new NotFoundHttpException();
    }
}
