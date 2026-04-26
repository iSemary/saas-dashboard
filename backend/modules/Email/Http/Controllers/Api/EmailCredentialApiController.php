<?php

namespace Modules\Email\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Modules\Email\DTOs\CreateEmailCredentialData;
use Modules\Email\DTOs\UpdateEmailCredentialData;
use Modules\Email\Http\Requests\StoreEmailCredentialRequest;
use Modules\Email\Http\Requests\UpdateEmailCredentialRequest;
use Modules\Email\Services\EmailCredentialService;

class EmailCredentialApiController extends ApiController
{
    public function __construct(protected EmailCredentialService $service) {}

    public function index(Request $request)
    {
        try {
            $credentials = $this->service->getAll();
            return response()->json(['data' => $credentials]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreEmailCredentialRequest $request)
    {
        try {
            $data = CreateEmailCredentialData::fromRequest($request);
            $credential = $this->service->create($data);

            return response()->json([
                'data' => $credential,
                'message' => translate('message.created_successfully')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $credential = $this->service->findOrFail($id);
            $credential->password = $credential->password ? '***' : null;
            return response()->json(['data' => $credential]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.resource_not_found'),
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(UpdateEmailCredentialRequest $request, $id)
    {
        try {
            $data = UpdateEmailCredentialData::fromRequest($request);
            $arrayData = $data->toArray();

            if (!isset($arrayData['password']) || $arrayData['password'] === '***') {
                unset($arrayData['password']);
            }

            $credential = $this->service->update($id, $arrayData);

            return response()->json([
                'data' => $credential,
                'message' => translate('message.updated_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete($id);
            return response()->json(['message' => translate('message.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function testConnection(Request $request, $id)
    {
        try {
            $request->validate(['test_email' => 'required|email']);
            $this->service->testConnection($id, $request->test_email);
            return response()->json(['message' => translate('message.action_completed')]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
