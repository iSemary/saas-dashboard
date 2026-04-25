<?php

use Illuminate\Support\Facades\Route;
use Modules\HR\Http\Controllers\Api\HrApiController;
use Modules\HR\Presentation\Http\Controllers\Api\AttendanceApiController;
use Modules\HR\Presentation\Http\Controllers\Api\DepartmentApiController;
use Modules\HR\Presentation\Http\Controllers\Api\EmployeeApiController;
use Modules\HR\Presentation\Http\Controllers\Api\HolidayApiController;
use Modules\HR\Presentation\Http\Controllers\Api\LeaveRequestApiController;
use Modules\HR\Presentation\Http\Controllers\Api\LeaveTypeApiController;
use Modules\HR\Presentation\Http\Controllers\Api\PositionApiController;
use Modules\HR\Presentation\Http\Controllers\Api\ShiftApiController;
use Modules\HR\Presentation\Http\Controllers\Api\WorkScheduleApiController;

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
    });

    // Positions
    Route::prefix('hr/positions')->name('hr.positions.')->group(function () {
        Route::get('/', [PositionApiController::class, 'index'])->name('index');
        Route::post('/', [PositionApiController::class, 'store'])->name('store');
        Route::get('/{id}', [PositionApiController::class, 'show'])->name('show');
        Route::put('/{id}', [PositionApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [PositionApiController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [PositionApiController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('/by-department/{departmentId}', [PositionApiController::class, 'byDepartment'])->name('by-department');
    });

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
    });

    // Shifts
    Route::prefix('hr/shifts')->name('hr.shifts.')->group(function () {
        Route::get('/', [ShiftApiController::class, 'index'])->name('index');
        Route::post('/', [ShiftApiController::class, 'store'])->name('store');
        Route::get('/active', [ShiftApiController::class, 'active'])->name('active');
        Route::get('/{id}', [ShiftApiController::class, 'show'])->name('show');
        Route::put('/{id}', [ShiftApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [ShiftApiController::class, 'destroy'])->name('destroy');
    });

    // Work Schedules
    Route::prefix('hr/work-schedules')->name('hr.work-schedules.')->group(function () {
        Route::get('/', [WorkScheduleApiController::class, 'index'])->name('index');
        Route::post('/', [WorkScheduleApiController::class, 'store'])->name('store');
        Route::get('/{id}', [WorkScheduleApiController::class, 'show'])->name('show');
        Route::put('/{id}', [WorkScheduleApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [WorkScheduleApiController::class, 'destroy'])->name('destroy');
        Route::get('/employee/{employeeId}/current', [WorkScheduleApiController::class, 'current'])->name('current');
        Route::get('/employee/{employeeId}', [WorkScheduleApiController::class, 'byEmployee'])->name('by-employee');
    });

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
    });

    // Leave Types
    Route::prefix('hr/leave-types')->name('hr.leave-types.')->group(function () {
        Route::get('/', [LeaveTypeApiController::class, 'index'])->name('index');
        Route::post('/', [LeaveTypeApiController::class, 'store'])->name('store');
        Route::get('/active', [LeaveTypeApiController::class, 'active'])->name('active');
        Route::get('/{id}', [LeaveTypeApiController::class, 'show'])->name('show');
        Route::put('/{id}', [LeaveTypeApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [LeaveTypeApiController::class, 'destroy'])->name('destroy');
    });

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
    });

    // Holidays
    Route::prefix('hr/holidays')->name('hr.holidays.')->group(function () {
        Route::get('/', [HolidayApiController::class, 'index'])->name('index');
        Route::post('/', [HolidayApiController::class, 'store'])->name('store');
        Route::get('/{id}', [HolidayApiController::class, 'show'])->name('show');
        Route::put('/{id}', [HolidayApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [HolidayApiController::class, 'destroy'])->name('destroy');
        Route::get('/year/{year}', [HolidayApiController::class, 'byYear'])->name('by-year');
        Route::get('/check', [HolidayApiController::class, 'check'])->name('check');
    });
});
