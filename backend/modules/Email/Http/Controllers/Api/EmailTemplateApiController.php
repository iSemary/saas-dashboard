<?php

namespace Modules\Email\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Modules\Email\DTOs\CreateEmailTemplateData;
use Modules\Email\DTOs\UpdateEmailTemplateData;
use Modules\Email\Http\Requests\StoreEmailTemplateRequest;
use Modules\Email\Http\Requests\UpdateEmailTemplateRequest;
use Modules\Email\Services\EmailTemplateService;
use Modules\Email\Services\EmailService;

class EmailTemplateApiController extends ApiController
{
    protected EmailService $emailService;
    protected EmailTemplateService $templateService;

    public function __construct(EmailService $emailService, EmailTemplateService $templateService)
    {
        $this->emailService = $emailService;
        $this->templateService = $templateService;
    }

    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search');
            $filters = $search ? ['search' => $search] : [];

            $templates = $this->templateService->list($filters, $perPage);

            return response()->json([
                'data' => $templates->items(),
                'current_page' => $templates->currentPage(),
                'last_page' => $templates->lastPage(),
                'per_page' => $templates->perPage(),
                'total' => $templates->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreEmailTemplateRequest $request)
    {
        try {
            $data = CreateEmailTemplateData::fromRequest($request);
            $template = $this->templateService->create($data->toArray());

            return response()->json([
                'data' => $template,
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
            $template = $this->templateService->findOrFail($id);
            return response()->json(['data' => $template]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.resource_not_found'),
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(UpdateEmailTemplateRequest $request, $id)
    {
        try {
            $data = UpdateEmailTemplateData::fromRequest($request);
            $template = $this->templateService->update($id, $data->toArray());

            return response()->json([
                'data' => $template,
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
            $this->templateService->delete($id);
            return response()->json(['message' => translate('message.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function sendTest(Request $request, $id)
    {
        try {
            $request->validate(['email' => 'required|email']);
            $this->emailService->sendTemplate($id, $request->email, []);
            return response()->json(['message' => translate('message.action_completed')]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
