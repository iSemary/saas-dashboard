<?php

namespace Modules\Development\Http\Controllers;

use App\Http\Controllers\ApiController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Modules\Development\Services\BackupService;

class BackupController extends ApiController implements HasMiddleware
{
    protected $service;

    public function __construct(BackupService $service)
    {
        $this->service = $service;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.backups', only: ['index']),
            new Middleware('permission:read.download', only: ['download']),
        ];
    }

    public function index()
    {
        if (request()->ajax()) {
            return $this->service->getDataTables();
        }

        $title = translate($this->service->model->pluralTitle);

        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate($this->service->model->pluralTitle)],
        ];

        $actionButtons = [];

        return view('landlord.developments.backups.index', compact('breadcrumbs', 'title'));
    }
}
