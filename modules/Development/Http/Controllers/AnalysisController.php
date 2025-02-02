<?php

namespace Modules\Development\Http\Controllers;

use App\Http\Controllers\ApiController;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class AnalysisController extends ApiController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.env_diff', only: ['showEnvDiff']),
        ];
    }

    public function showEnvDiff()
    {
        $envKeys = $this->getEnvKeys(base_path('.env'));
        $envExampleKeys = $this->getEnvKeys(base_path('.env.example'));

        $missingInEnv = array_diff($envExampleKeys, $envKeys);
        $missingInEnvExample = array_diff($envKeys, $envExampleKeys);

        $data = [
            'env_count' => count($envKeys),
            'env_example_count' => count($envExampleKeys),
            'missing_in_env' => $missingInEnv,
            'missing_in_env_example' => $missingInEnvExample,
        ];

        if (empty($missingInEnv) && empty($missingInEnvExample)) {
            $status = 'success';
            $message = "Both files have the same number of keys ({$data['env_count']}).";
        } else {
            $status = 'error';
            $message = 'There are differences between the two files.';
        }

        $data['status'] = $status;
        $data['message'] = $message;

        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => "Code Builder"],
        ];

        return view('landlord.developments.env-diff.index', compact('breadcrumbs', 'data'));
    }

    private function getEnvKeys($filePath)
    {
        if (!file_exists($filePath)) {
            return [];
        }

        $keys = [];
        foreach (file($filePath) as $line) {
            $line = trim($line);
            if (!empty($line) && strpos($line, '=') !== false && substr($line, 0, 1) !== '#') {
                $keys[] = explode('=', $line, 2)[0];
            }
        }

        return $keys;
    }
}
