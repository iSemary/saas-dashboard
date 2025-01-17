<?php

namespace Modules\Notification\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Notification\Services\NotificationService;

class NotificationController extends ApiController
{
    protected $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        $title = translate($this->service->model->pluralTitle);

        $layoutPrefix = auth()->user()->getCurrentTypeName();

        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate($this->service->model->pluralTitle)],
        ];

        return view('user.notifications.index', compact('breadcrumbs', 'title', 'layoutPrefix'));
    }

    public function list()
    {
        return $this->return(200, "List of notifications", ['data' => $this->service->list()]);
    }

    public function markAllAsRead()
    {
        $this->service->markAllAsRead();
        return $this->return(200, "Marked all as read");
    }

    public function markAsRead($id)
    {
        $this->service->markAsRead($id);
        return $this->return(200, "Marked as read");
    }

    public function markAsUnread($id)
    {
        $this->service->markAsUnRead($id);
        return $this->return(200, "Marked as unread");
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->return(200, "Deleted successfully");
    }

    public function restore($id)
    {
        $this->service->restore($id);
        return $this->return(200, "Deleted successfully");
    }
}
