<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\TimeManagement\Domain\Entities\WorkCalendar;
use Modules\TimeManagement\Domain\Entities\ShiftTemplate;
use Modules\TimeManagement\Domain\Entities\WorkSchedule;
use Modules\TimeManagement\Domain\Entities\TimePolicy;
use Modules\TimeManagement\Domain\Entities\CalendarEvent;
use Modules\TimeManagement\Domain\Entities\Webhook as TmWebhook;

class WorkCalendarController extends Controller
{
    use ApiResponseEnvelope;
    public function index(Request $request): JsonResponse { return $this->apiSuccess(WorkCalendar::orderBy('created_at', 'desc')->paginate($request->get('per_page', 15))); }
    public function store(Request $request): JsonResponse { return $this->apiSuccess(WorkCalendar::create(array_merge($request->all(), ['created_by' => $request->user()->id])), translate('message.created_successfully'), 201); }
    public function show(string $id): JsonResponse { return $this->apiSuccess(WorkCalendar::findOrFail($id)); }
    public function update(Request $request, string $id): JsonResponse { $m = WorkCalendar::findOrFail($id); $m->update($request->all()); return $this->apiSuccess($m->fresh(), translate('message.updated_successfully')); }
    public function destroy(string $id): JsonResponse { WorkCalendar::findOrFail($id)->delete(); return $this->apiSuccess(null, translate('message.deleted_successfully')); }
}

class ShiftTemplateController extends Controller
{
    use ApiResponseEnvelope;
    public function index(Request $request): JsonResponse { return $this->apiSuccess(ShiftTemplate::orderBy('name')->paginate($request->get('per_page', 15))); }
    public function store(Request $request): JsonResponse { return $this->apiSuccess(ShiftTemplate::create(array_merge($request->all(), ['created_by' => $request->user()->id])), translate('message.created_successfully'), 201); }
    public function show(string $id): JsonResponse { return $this->apiSuccess(ShiftTemplate::findOrFail($id)); }
    public function update(Request $request, string $id): JsonResponse { $m = ShiftTemplate::findOrFail($id); $m->update($request->all()); return $this->apiSuccess($m->fresh(), translate('message.updated_successfully')); }
    public function destroy(string $id): JsonResponse { ShiftTemplate::findOrFail($id)->delete(); return $this->apiSuccess(null, translate('message.deleted_successfully')); }
}

class WorkScheduleController extends Controller
{
    use ApiResponseEnvelope;
    public function index(Request $request): JsonResponse { return $this->apiSuccess(WorkSchedule::with(['user', 'workCalendar', 'shiftTemplate'])->orderBy('effective_from', 'desc')->paginate($request->get('per_page', 15))); }
    public function store(Request $request): JsonResponse { return $this->apiSuccess(WorkSchedule::create($request->all()), translate('message.created_successfully'), 201); }
    public function show(string $id): JsonResponse { return $this->apiSuccess(WorkSchedule::findOrFail($id)); }
    public function update(Request $request, string $id): JsonResponse { $m = WorkSchedule::findOrFail($id); $m->update($request->all()); return $this->apiSuccess($m->fresh(), translate('message.updated_successfully')); }
    public function destroy(string $id): JsonResponse { WorkSchedule::findOrFail($id)->delete(); return $this->apiSuccess(null, translate('message.deleted_successfully')); }
}

class TimePolicyController extends Controller
{
    use ApiResponseEnvelope;
    public function index(Request $request): JsonResponse { return $this->apiSuccess(TimePolicy::orderBy('created_at', 'desc')->paginate($request->get('per_page', 15))); }
    public function store(Request $request): JsonResponse { return $this->apiSuccess(TimePolicy::create(array_merge($request->all(), ['created_by' => $request->user()->id])), translate('message.created_successfully'), 201); }
    public function show(string $id): JsonResponse { return $this->apiSuccess(TimePolicy::findOrFail($id)); }
    public function update(Request $request, string $id): JsonResponse { $m = TimePolicy::findOrFail($id); $m->update($request->all()); return $this->apiSuccess($m->fresh(), translate('message.updated_successfully')); }
    public function destroy(string $id): JsonResponse { TimePolicy::findOrFail($id)->delete(); return $this->apiSuccess(null, translate('message.deleted_successfully')); }
}
