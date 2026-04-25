<?php

use Illuminate\Support\Facades\Route;
use Modules\HR\Http\Controllers\Api\HrApiController;
use Modules\HR\Presentation\Http\Controllers\Api\AttendanceApiController;
use Modules\HR\Presentation\Http\Controllers\Api\AnnouncementApiController;
use Modules\HR\Presentation\Http\Controllers\Api\AssetApiController;
use Modules\HR\Presentation\Http\Controllers\Api\DepartmentApiController;
use Modules\HR\Presentation\Http\Controllers\Api\EmployeeApiController;
use Modules\HR\Presentation\Http\Controllers\Api\ExpenseApiController;
use Modules\HR\Presentation\Http\Controllers\Api\HolidayApiController;
use Modules\HR\Presentation\Http\Controllers\Api\HrReportApiController;
use Modules\HR\Presentation\Http\Controllers\Api\OnboardingApiController;
use Modules\HR\Presentation\Http\Controllers\Api\PayrollApiController;
use Modules\HR\Presentation\Http\Controllers\Api\PerformanceCycleApiController;
use Modules\HR\Presentation\Http\Controllers\Api\LeaveRequestApiController;
use Modules\HR\Presentation\Http\Controllers\Api\LeaveTypeApiController;
use Modules\HR\Presentation\Http\Controllers\Api\PositionApiController;
use Modules\HR\Presentation\Http\Controllers\Api\RecruitmentApiController;
use Modules\HR\Presentation\Http\Controllers\Api\SelfServiceApiController;
use Modules\HR\Presentation\Http\Controllers\Api\ShiftApiController;
use Modules\HR\Presentation\Http\Controllers\Api\TrainingApiController;
use Modules\HR\Presentation\Http\Controllers\Api\WorkScheduleApiController;
use Modules\HR\Http\Middleware\EmployeeContextMiddleware;

// ─── Tenant HR Module ────────────────────────────────────────────
Route::prefix('tenant')->name('tenant.')->middleware(['auth:api', 'tenant_roles', 'throttle:60,1'])->group(function () {

    // Dashboard
    Route::get('modules/hr', [HrApiController::class, 'index'])->name('modules.hr');

    // Departments
    Route::prefix('hr/departments')->name('hr.departments.')->group(function () {
        Route::get('/', [DepartmentApiController::class, 'index'])->name('index');
        Route::get('/tree', [DepartmentApiController::class, 'tree'])->name('tree');
        Route::post('/', [DepartmentApiController::class, 'store'])->name('store');
        Route::get('/{id}', [DepartmentApiController::class, 'show'])->name('show');
        Route::put('/{id}', [DepartmentApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [DepartmentApiController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [DepartmentApiController::class, 'bulkDelete'])->name('bulk-delete');
    })->middleware(['permission:hr.departments.view']);

    // Positions
    Route::prefix('hr/positions')->name('hr.positions.')->group(function () {
        Route::get('/', [PositionApiController::class, 'index'])->name('index');
        Route::post('/', [PositionApiController::class, 'store'])->name('store');
        Route::get('/{id}', [PositionApiController::class, 'show'])->name('show');
        Route::put('/{id}', [PositionApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [PositionApiController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [PositionApiController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('/by-department/{departmentId}', [PositionApiController::class, 'byDepartment'])->name('by-department');
    })->middleware(['permission:hr.positions.view']);

    // Employees
    Route::prefix('hr/employees')->name('hr.employees.')->group(function () {
        Route::get('/', [EmployeeApiController::class, 'index'])->name('index');
        Route::post('/', [EmployeeApiController::class, 'store'])->name('store');
        Route::get('/org-chart', [EmployeeApiController::class, 'orgChart'])->name('org-chart');
        Route::get('/{id}', [EmployeeApiController::class, 'show'])->name('show');
        Route::put('/{id}', [EmployeeApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [EmployeeApiController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [EmployeeApiController::class, 'bulkDelete'])->name('bulk-delete');

        // Employee actions
        Route::post('/{id}/transfer', [EmployeeApiController::class, 'transfer'])->name('transfer');
        Route::post('/{id}/promote', [EmployeeApiController::class, 'promote'])->name('promote');
        Route::post('/{id}/terminate', [EmployeeApiController::class, 'terminate'])->name('terminate');
        Route::post('/{id}/reactivate', [EmployeeApiController::class, 'reactivate'])->name('reactivate');

        // Avatar
        Route::post('/{id}/avatar', [EmployeeApiController::class, 'uploadAvatar'])->name('avatar.upload');
        Route::delete('/{id}/avatar', [EmployeeApiController::class, 'removeAvatar'])->name('avatar.remove');

        // Documents sub-resource
        Route::get('/{id}/documents', [EmployeeApiController::class, 'documents'])->name('documents.index');
        Route::post('/{id}/documents', [EmployeeApiController::class, 'storeDocument'])->name('documents.store');
        Route::delete('/{id}/documents/{documentId}', [EmployeeApiController::class, 'destroyDocument'])->name('documents.destroy');

        // Contracts sub-resource
        Route::get('/{id}/contracts', [EmployeeApiController::class, 'contracts'])->name('contracts.index');
        Route::post('/{id}/contracts', [EmployeeApiController::class, 'storeContract'])->name('contracts.store');
        Route::delete('/{id}/contracts/{contractId}', [EmployeeApiController::class, 'destroyContract'])->name('contracts.destroy');

        // Employment History
        Route::get('/{id}/history', [EmployeeApiController::class, 'history'])->name('history');

        // Import
        Route::post('/import', [EmployeeApiController::class, 'import'])->name('import');
    })->middleware(['permission:hr.employees.view']);

    // Shifts
    Route::prefix('hr/shifts')->name('hr.shifts.')->group(function () {
        Route::get('/', [ShiftApiController::class, 'index'])->name('index');
        Route::post('/', [ShiftApiController::class, 'store'])->name('store');
        Route::get('/active', [ShiftApiController::class, 'active'])->name('active');
        Route::get('/{id}', [ShiftApiController::class, 'show'])->name('show');
        Route::put('/{id}', [ShiftApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [ShiftApiController::class, 'destroy'])->name('destroy');
    })->middleware(['permission:hr.shifts.view']);

    // Work Schedules
    Route::prefix('hr/work-schedules')->name('hr.work-schedules.')->group(function () {
        Route::get('/', [WorkScheduleApiController::class, 'index'])->name('index');
        Route::post('/', [WorkScheduleApiController::class, 'store'])->name('store');
        Route::get('/{id}', [WorkScheduleApiController::class, 'show'])->name('show');
        Route::put('/{id}', [WorkScheduleApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [WorkScheduleApiController::class, 'destroy'])->name('destroy');
        Route::get('/employee/{employeeId}/current', [WorkScheduleApiController::class, 'current'])->name('current');
        Route::get('/employee/{employeeId}', [WorkScheduleApiController::class, 'byEmployee'])->name('by-employee');
    })->middleware(['permission:hr.schedules.view']);

    // Attendance
    Route::prefix('hr/attendance')->name('hr.attendance.')->group(function () {
        Route::get('/', [AttendanceApiController::class, 'index'])->name('index');
        Route::post('/', [AttendanceApiController::class, 'store'])->name('store');
        Route::get('/{id}', [AttendanceApiController::class, 'show'])->name('show');
        Route::put('/{id}', [AttendanceApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [AttendanceApiController::class, 'destroy'])->name('destroy');
        Route::post('/check-in', [AttendanceApiController::class, 'checkIn'])->name('check-in');
        Route::post('/{id}/check-out', [AttendanceApiController::class, 'checkOut'])->name('check-out');
        Route::post('/{id}/approve', [AttendanceApiController::class, 'approve'])->name('approve');
        Route::get('/pending-approvals', [AttendanceApiController::class, 'pendingApprovals'])->name('pending-approvals');
        Route::get('/today/{employeeId}', [AttendanceApiController::class, 'today'])->name('today');
        Route::get('/employee/{employeeId}/range', [AttendanceApiController::class, 'byDateRange'])->name('by-date-range');
    })->middleware(['permission:hr.attendance.view']);

    // Leave Types
    Route::prefix('hr/leave-types')->name('hr.leave-types.')->group(function () {
        Route::get('/', [LeaveTypeApiController::class, 'index'])->name('index');
        Route::post('/', [LeaveTypeApiController::class, 'store'])->name('store');
        Route::get('/active', [LeaveTypeApiController::class, 'active'])->name('active');
        Route::get('/{id}', [LeaveTypeApiController::class, 'show'])->name('show');
        Route::put('/{id}', [LeaveTypeApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [LeaveTypeApiController::class, 'destroy'])->name('destroy');
    })->middleware(['permission:hr.leave_types.view']);

    // Leave Requests
    Route::prefix('hr/leave-requests')->name('hr.leave-requests.')->group(function () {
        Route::get('/', [LeaveRequestApiController::class, 'index'])->name('index');
        Route::post('/', [LeaveRequestApiController::class, 'store'])->name('store');
        Route::get('/{id}', [LeaveRequestApiController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [LeaveRequestApiController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [LeaveRequestApiController::class, 'reject'])->name('reject');
        Route::post('/{id}/cancel', [LeaveRequestApiController::class, 'cancel'])->name('cancel');
        Route::get('/pending', [LeaveRequestApiController::class, 'pending'])->name('pending');
        Route::get('/employee/{employeeId}', [LeaveRequestApiController::class, 'byEmployee'])->name('by-employee');
        Route::get('/check-overlap', [LeaveRequestApiController::class, 'checkOverlap'])->name('check-overlap');
    })->middleware(['permission:hr.leave_requests.view']);

    // Holidays
    Route::prefix('hr/holidays')->name('hr.holidays.')->group(function () {
        Route::get('/', [HolidayApiController::class, 'index'])->name('index');
        Route::post('/', [HolidayApiController::class, 'store'])->name('store');
        Route::get('/{id}', [HolidayApiController::class, 'show'])->name('show');
        Route::put('/{id}', [HolidayApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [HolidayApiController::class, 'destroy'])->name('destroy');
        Route::get('/year/{year}', [HolidayApiController::class, 'byYear'])->name('by-year');
        Route::get('/check', [HolidayApiController::class, 'check'])->name('check');
    })->middleware(['permission:hr.leave_types.view']);

    // Payroll
    Route::prefix('hr/payrolls')->name('hr.payrolls.')->group(function () {
        Route::get('/', [PayrollApiController::class, 'index'])->name('index');
        Route::get('/{id}', [PayrollApiController::class, 'show'])->name('show');
        Route::post('/generate', [PayrollApiController::class, 'generate'])->name('generate');
        Route::post('/{id}/calculate', [PayrollApiController::class, 'calculate'])->name('calculate');
        Route::post('/{id}/approve', [PayrollApiController::class, 'approve'])->name('approve');
        Route::post('/{id}/mark-paid', [PayrollApiController::class, 'markPaid'])->name('mark-paid');
        Route::delete('/{id}', [PayrollApiController::class, 'destroy'])->name('destroy');
        Route::get('/employee/{employeeId}', [PayrollApiController::class, 'byEmployee'])->name('by-employee');
        Route::get('/status/{status}', [PayrollApiController::class, 'byStatus'])->name('by-status');
    })->middleware(['permission:hr.payroll.view']);

    // Performance Cycles
    Route::prefix('hr/performance-cycles')->name('hr.performance-cycles.')->group(function () {
        Route::get('/', [PerformanceCycleApiController::class, 'index'])->name('index');
        Route::post('/', [PerformanceCycleApiController::class, 'store'])->name('store');
        Route::get('/{id}', [PerformanceCycleApiController::class, 'show'])->name('show');
        Route::put('/{id}', [PerformanceCycleApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [PerformanceCycleApiController::class, 'destroy'])->name('destroy');
        Route::get('/active', [PerformanceCycleApiController::class, 'active'])->name('active');
        Route::get('/open', [PerformanceCycleApiController::class, 'open'])->name('open');
    })->middleware(['permission:hr.performance.view']);

    // Recruitment
    Route::prefix('hr/recruitment')->name('hr.recruitment.')->group(function () {
        // Job Openings
        Route::get('/jobs', [RecruitmentApiController::class, 'indexJobs'])->name('jobs.index');
        Route::post('/jobs', [RecruitmentApiController::class, 'storeJob'])->name('jobs.store');
        Route::get('/jobs/{id}', [RecruitmentApiController::class, 'showJob'])->name('jobs.show');
        Route::put('/jobs/{id}', [RecruitmentApiController::class, 'updateJob'])->name('jobs.update');
        Route::delete('/jobs/{id}', [RecruitmentApiController::class, 'destroyJob'])->name('jobs.destroy');

        // Candidates
        Route::get('/candidates', [RecruitmentApiController::class, 'indexCandidates'])->name('candidates.index');
        Route::post('/candidates', [RecruitmentApiController::class, 'storeCandidate'])->name('candidates.store');
        Route::get('/candidates/{id}', [RecruitmentApiController::class, 'showCandidate'])->name('candidates.show');
        Route::put('/candidates/{id}', [RecruitmentApiController::class, 'updateCandidate'])->name('candidates.update');
        Route::delete('/candidates/{id}', [RecruitmentApiController::class, 'destroyCandidate'])->name('candidates.destroy');

        // Applications and stage movement
        Route::get('/applications', [RecruitmentApiController::class, 'indexApplications'])->name('applications.index');
        Route::get('/applications/{id}', [RecruitmentApiController::class, 'showApplication'])->name('applications.show');
        Route::post('/applications', [RecruitmentApiController::class, 'apply'])->name('applications.apply');
        Route::post('/applications/{id}/advance', [RecruitmentApiController::class, 'advance'])->name('applications.advance');
        Route::post('/applications/{id}/reject', [RecruitmentApiController::class, 'reject'])->name('applications.reject');

        // Interviews
        Route::get('/interviews', [RecruitmentApiController::class, 'listInterviews'])->name('interviews.index');
        Route::post('/applications/{applicationId}/interviews', [RecruitmentApiController::class, 'scheduleInterview'])->name('interviews.schedule');

        // Offers
        Route::get('/offers', [RecruitmentApiController::class, 'listOffers'])->name('offers.index');
        Route::post('/applications/{applicationId}/offers', [RecruitmentApiController::class, 'makeOffer'])->name('offers.make');
        Route::post('/offers/{id}/send', [RecruitmentApiController::class, 'sendOffer'])->name('offers.send');
        Route::post('/offers/{id}/accept', [RecruitmentApiController::class, 'acceptOffer'])->name('offers.accept');
        Route::post('/offers/{id}/reject', [RecruitmentApiController::class, 'rejectOffer'])->name('offers.reject');

        // Pipeline stages
        Route::get('/pipeline-stages', [RecruitmentApiController::class, 'indexPipelineStages'])->name('pipeline-stages.index');
        Route::post('/pipeline-stages', [RecruitmentApiController::class, 'storePipelineStage'])->name('pipeline-stages.store');
        Route::put('/pipeline-stages/{id}', [RecruitmentApiController::class, 'updatePipelineStage'])->name('pipeline-stages.update');
        Route::delete('/pipeline-stages/{id}', [RecruitmentApiController::class, 'destroyPipelineStage'])->name('pipeline-stages.destroy');
    })->middleware(['permission:hr.recruitment.view']);

    // Onboarding
    Route::prefix('hr/onboarding')->name('hr.onboarding.')->middleware(['permission:hr.onboarding.view'])->group(function () {
        Route::get('/templates', [OnboardingApiController::class, 'templates'])->name('templates.index');
        Route::post('/templates', [OnboardingApiController::class, 'storeTemplate'])->name('templates.store');
        Route::put('/templates/{id}', [OnboardingApiController::class, 'updateTemplate'])->name('templates.update');
        Route::get('/processes', [OnboardingApiController::class, 'processes'])->name('processes.index');
        Route::post('/processes', [OnboardingApiController::class, 'storeProcess'])->name('processes.store');
        Route::put('/processes/{id}', [OnboardingApiController::class, 'updateProcess'])->name('processes.update');
    });

    // Training
    Route::prefix('hr/training')->name('hr.training.')->middleware(['permission:hr.training.view'])->group(function () {
        Route::get('/courses', [TrainingApiController::class, 'courses'])->name('courses.index');
        Route::post('/courses', [TrainingApiController::class, 'storeCourse'])->name('courses.store');
        Route::get('/enrollments', [TrainingApiController::class, 'enrollments'])->name('enrollments.index');
        Route::post('/enrollments', [TrainingApiController::class, 'storeEnrollment'])->name('enrollments.store');
        Route::get('/certifications', [TrainingApiController::class, 'certifications'])->name('certifications.index');
    });

    // Assets
    Route::prefix('hr/assets')->name('hr.assets.')->middleware(['permission:hr.assets.view'])->group(function () {
        Route::get('/categories', [AssetApiController::class, 'categories'])->name('categories.index');
        Route::post('/categories', [AssetApiController::class, 'storeCategory'])->name('categories.store');
        Route::get('/', [AssetApiController::class, 'assets'])->name('index');
        Route::post('/', [AssetApiController::class, 'storeAsset'])->name('store');
        Route::get('/assignments', [AssetApiController::class, 'assignments'])->name('assignments.index');
        Route::post('/assignments', [AssetApiController::class, 'storeAssignment'])->name('assignments.store');
    });

    // Expenses
    Route::prefix('hr/expenses')->name('hr.expenses.')->middleware(['permission:hr.expenses.view'])->group(function () {
        Route::get('/categories', [ExpenseApiController::class, 'categories'])->name('categories.index');
        Route::post('/categories', [ExpenseApiController::class, 'storeCategory'])->name('categories.store');
        Route::get('/claims', [ExpenseApiController::class, 'claims'])->name('claims.index');
        Route::post('/claims', [ExpenseApiController::class, 'storeClaim'])->name('claims.store');
    });

    // Announcements & Policies
    Route::prefix('hr/communication')->name('hr.communication.')->middleware(['permission:hr.announcements.view'])->group(function () {
        Route::get('/announcements', [AnnouncementApiController::class, 'announcements'])->name('announcements.index');
        Route::post('/announcements', [AnnouncementApiController::class, 'storeAnnouncement'])->name('announcements.store');
        Route::get('/policies', [AnnouncementApiController::class, 'policies'])->name('policies.index');
        Route::post('/policies', [AnnouncementApiController::class, 'storePolicy'])->name('policies.store');
    });

    // Self-service
    Route::prefix('hr/me')->name('hr.me.')->middleware([EmployeeContextMiddleware::class])->group(function () {
        Route::get('/', [SelfServiceApiController::class, 'me'])->middleware(['permission:view.own.hr.profile'])->name('profile');
        Route::get('/leaves', [SelfServiceApiController::class, 'leaves'])->middleware(['permission:view.own.hr.leaves'])->name('leaves');
        Route::get('/attendance', [SelfServiceApiController::class, 'attendance'])->middleware(['permission:view.own.hr.attendance'])->name('attendance');
        Route::get('/payroll', [SelfServiceApiController::class, 'payroll'])->middleware(['permission:view.own.hr.payroll'])->name('payroll');
        Route::get('/goals', [SelfServiceApiController::class, 'goals'])->middleware(['permission:view.own.hr.goals'])->name('goals');
        Route::get('/assets', [SelfServiceApiController::class, 'assets'])->middleware(['permission:view.own.hr.assets'])->name('assets');
    });

    // Reports
    Route::prefix('hr/reports')->name('hr.reports.')->middleware(['permission:hr.reports.view'])->group(function () {
        Route::get('/headcount', [HrReportApiController::class, 'headcount'])->name('headcount');
        Route::get('/leave-usage', [HrReportApiController::class, 'leaveUsage'])->name('leave-usage');
        Route::get('/attendance-summary', [HrReportApiController::class, 'attendanceSummary'])->name('attendance-summary');
        Route::get('/payroll-summary', [HrReportApiController::class, 'payrollSummary'])->name('payroll-summary');
        Route::get('/recruitment-funnel', [HrReportApiController::class, 'recruitmentFunnel'])->name('recruitment-funnel');
    });
});
