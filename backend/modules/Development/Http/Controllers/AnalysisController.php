<?php

namespace Modules\Development\Http\Controllers;

use App\Http\Controllers\ApiController;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class AnalysisController extends ApiController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.env_diff', only: ['showEnvDiff']),
            new Middleware('permission:read.system_status', only: ['showSystemStatus', 'checkIndividualService']),
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

    public function showSystemStatus()
    {
        $statuses = $this->getSystemStatuses();
        
        return view('landlord.developments.system-status.index', compact('statuses'));
    }
    
    public function checkSystemStatus()
    {
        $statuses = $this->getSystemStatuses();
        
        return response()->json($statuses);
    }
    
    public function checkIndividualService(Request $request)
    {
        $service = $request->input('service');
        $status = null;
        
        switch ($service) {
            case 'database':
                $status = $this->checkDatabaseConnection();
                break;
            case 'websocket':
                $status = $this->checkWebsocketServer();
                break;
            case 'queue':
                $status = $this->checkQueueListener();
                break;
            case 'redis':
                $status = $this->checkRedis();
                break;
            case 'supervisor':
                $status = $this->checkSupervisor();
                break;
            case 'disk_space':
                $status = $this->checkDiskSpace();
                break;
            case 'php_version':
                $status = $this->checkPhpVersion();
                break;
            case 'memory_usage':
                $status = $this->checkMemoryUsage();
                break;
            case 'cache_status':
                $status = $this->checkCacheStatus();
                break;
            default:
                return response()->json(['error' => 'Invalid service specified'], 400);
        }
        
        return response()->json(['status' => $status]);
    }
    
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            
            return response()->json([
                'success' => true,
                'message' => translate('message.action_completed')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed') . $e->getMessage()
            ], 500);
        }
    }
    
    public function restartQueue()
    {
        try {
            Artisan::call('queue:restart');
            
            return response()->json([
                'success' => true,
                'message' => translate('message.action_completed')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed') . $e->getMessage()
            ], 500);
        }
    }
    
    public function restartSupervisor()
    {
        try {
            $process = new Process(['supervisorctl', 'reload']);
            $process->run();
            
            if ($process->isSuccessful()) {
                return response()->json([
                    'success' => true,
                    'message' => translate('message.action_completed')
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => translate('message.operation_failed') . $process->getErrorOutput()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed') . $e->getMessage()
            ], 500);
        }
    }
    
    private function getSystemStatuses()
    {
        return [
            'database' => $this->checkDatabaseConnection(),
            'websocket' => $this->checkWebsocketServer(),
            'queue' => $this->checkQueueListener(),
            'redis' => $this->checkRedis(),
            'supervisor' => $this->checkSupervisor(),
            'disk_space' => $this->checkDiskSpace(),
            'php_version' => $this->checkPhpVersion(),
            'memory_usage' => $this->checkMemoryUsage(),
            'cache_status' => $this->checkCacheStatus()
        ];
    }

    private function checkDatabaseConnection()
    {
        try {
            DB::connection()->getPdo();
            return [
                'status' => 'success',
                'message' => translate('message.action_completed')
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => translate('message.operation_failed') . ': ' . $e->getMessage()
            ];
        }
    }

    private function checkWebsocketServer()
    {
        try {
            $socketServer = env('NODE_SOCKET_SERVER', 'http://localhost:4000') . "/health";
            $response = Http::timeout(5)->get($socketServer);

            if ($response->successful()) {
                return [
                    'status' => 'success',
                    'message' => translate('message.action_completed')
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => translate('message.operation_failed')
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => translate('message.operation_failed') . ': ' . $e->getMessage()
            ];
        }
    }

    private function checkQueueListener()
    {
        try {
            // Check if artisan queue:work process is running
            $process = new Process(['ps', 'aux']);
            $process->run();
            $output = $process->getOutput();

            if (strpos($output, 'artisan queue:') !== false) {
                return [
                    'status' => 'success',
                    'message' => translate('message.action_completed')
                ];
            } else {
                return [
                    'status' => 'warning',
                    'message' => translate('message.operation_failed')
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => translate('message.operation_failed') . ': ' . $e->getMessage()
            ];
        }
    }

    private function checkRedis()
    {
        try {
            $redis = Redis::connection();
            $pingResponse = $redis->ping();

            if ($pingResponse) {
                return [
                    'status' => 'success',
                    'message' => translate('message.action_completed')
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => translate('message.operation_failed')
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => translate('message.operation_failed') . ': ' . $e->getMessage()
            ];
        }
    }

    private function checkSupervisor()
    {
        try {
            $process = new Process(['supervisorctl', 'status']);
            $process->run();

            if ($process->isSuccessful()) {
                return [
                    'status' => 'success',
                    'message' => translate('message.action_completed')
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => translate('message.operation_failed')
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => translate('message.operation_failed') . ': ' . $e->getMessage()
            ];
        }
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

    private function checkDiskSpace()
    {
        try {
            $freeSpace = disk_free_space("/");
            $totalSpace = disk_total_space("/");

            $freePercentage = ($freeSpace / $totalSpace) * 100;

            return [
                'status' => $freePercentage > 10 ? 'success' : 'warning',
                'message' => "Disk space is at " . round($freePercentage, 2) . "% free"
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => translate('message.operation_failed') . $e->getMessage()
            ];
        }
    }

    private function checkPhpVersion()
    {
        $phpVersion = phpversion();
        return [
            'status' => version_compare($phpVersion, '8.0', '>=') ? 'success' : 'warning',
            'message' => "PHP Version: $phpVersion"
        ];
    }

    private function checkMemoryUsage()
    {
        try {
            $memoryUsage = memory_get_usage(true) / 1024 / 1024;
            $memoryLimit = ini_get('memory_limit');

            return [
                'status' => 'success',
                'message' => "Memory usage: " . round($memoryUsage, 2) . " MB / Limit: $memoryLimit"
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => translate('message.operation_failed') . $e->getMessage()
            ];
        }
    }

    private function checkCacheStatus()
    {
        try {
            $cacheDriver = config('cache.default');
            return [
                'status' => 'success',
                'message' => "Cache driver in use: $cacheDriver"
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => translate('message.operation_failed') . $e->getMessage()
            ];
        }
    }
}