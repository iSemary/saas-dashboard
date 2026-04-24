<?php

namespace Modules\Development\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Routing\Controller;

class EnvDiffApiController extends Controller
{
    use ApiResponseEnvelope;

    public function index()
    {
        $envPath = base_path('.env');
        $examplePath = base_path('.env.example');

        $envKeys = $this->parseEnvFile($envPath);
        $exampleKeys = $this->parseEnvFile($examplePath);

        $missingInEnv = array_diff(array_keys($exampleKeys), array_keys($envKeys));
        $missingInExample = array_diff(array_keys($envKeys), array_keys($exampleKeys));

        $isSuccess = empty($missingInEnv) && empty($missingInExample);

        $data = [
            'status' => $isSuccess ? 'success' : 'error',
            'message' => $isSuccess ? 'All keys are in sync' : 'Env files are out of sync',
            'env_count' => count($envKeys),
            'env_example_count' => count($exampleKeys),
            'missing_in_env' => array_values($missingInEnv),
            'missing_in_env_example' => array_values($missingInExample),
        ];

        return $this->apiSuccess($data);
    }

    private function parseEnvFile(string $path): array
    {
        if (!file_exists($path)) return [];
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $keys = [];
        foreach ($lines as $line) {
            if (str_starts_with($line, '#')) continue;
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $keys[trim($parts[0])] = trim($parts[1]);
            }
        }
        return $keys;
    }
}
