<?php

namespace Modules\Notification\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Notification\Services\NotificationService;
use Illuminate\Http\Request;

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
        $filters = request()->only(['status', 'type', 'priority', 'search', 'per_page']);
        return $this->return(200, "List of notifications", ['data' => $this->service->list($filters)]);
    }

    public function stats()
    {
        return $this->return(200, "Notification statistics", $this->service->getStats());
    }

    public function unreadCount()
    {
        $count = $this->service->getUnreadCount();
        return $this->return(200, "Unread count retrieved", ['count' => $count]);
    }

    public function getPreferences()
    {
        $user = auth()->user();
        $settingsService = app(\Modules\Auth\Services\SettingsService::class);
        $result = $settingsService->getNotificationSettings($user->id);
        
        if ($result['success']) {
            $settings = $result['data'];
            return $this->return(200, "Notification preferences retrieved", [
                'email' => $settings['notifications_email'] ?? true,
                'push' => $settings['notifications_push'] ?? true,
                'in_app' => true, // Always enabled
            ]);
        }
        
        return $this->return(200, "Notification preferences retrieved", [
            'email' => true,
            'push' => true,
            'in_app' => true,
        ]);
    }

    public function updatePreferences(Request $request)
    {
        $user = auth()->user();
        $settingsService = app(\Modules\Auth\Services\SettingsService::class);
        
        $data = [
            'notifications_email' => $request->input('email', true),
            'notifications_push' => $request->input('push', true),
        ];
        
        $result = $settingsService->updateNotificationSettings($user->id, $data);
        
        if ($result['success']) {
            return $this->return(200, "Notification preferences updated", [
                'email' => $data['notifications_email'],
                'push' => $data['notifications_push'],
                'in_app' => true,
            ]);
        }
        
        return $this->return(400, $result['message'] ?? "Failed to update preferences");
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
