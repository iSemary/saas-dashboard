<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Entities\UserMeta;

class UserMetaApiController extends Controller
{
    use ApiResponseEnvelope;

    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $metas = UserMeta::where('user_id', $userId)->get();
        
        $data = [];
        foreach ($metas as $meta) {
            $data[$meta->meta_key] = $meta->meta_value;
        }
        
        return $this->apiSuccess($data);
    }

    public function show(Request $request, string $key)
    {
        $userId = $request->user()->id;
        $meta = UserMeta::where('user_id', $userId)
            ->where('meta_key', $key)
            ->first();
        
        if (!$meta) {
            return $this->apiSuccess(null);
        }
        
        return $this->apiSuccess($meta->meta_value);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'nullable|string|max:65535',
        ]);

        $userId = $request->user()->id;
        
        $meta = UserMeta::updateOrCreate(
            [
                'user_id' => $userId,
                'meta_key' => $validated['key'],
            ],
            [
                'meta_value' => $validated['value'] ?? '',
            ]
        );

        return $this->apiSuccess($meta, translate('message.action_completed'));
    }

    public function destroy(Request $request, string $key)
    {
        $userId = $request->user()->id;
        
        UserMeta::where('user_id', $userId)
            ->where('meta_key', $key)
            ->delete();
        
        return $this->apiSuccess(null, translate('message.action_completed'));
    }
}
