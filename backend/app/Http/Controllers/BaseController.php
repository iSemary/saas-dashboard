<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

/**
 * Base Controller implementing SOLID principles
 *
 * Single Responsibility: Handles common controller functionality
 * Open/Closed: Can be extended without modification
 * Liskov Substitution: Can be substituted by any controller extending it
 * Interface Segregation: Implements only necessary interfaces
 * Dependency Inversion: Depends on abstractions, not concretions
 */
abstract class BaseController extends ApiController implements HasMiddleware
{
    /**
     * Get the service instance
     */
    abstract protected function getService();

    /**
     * Get the model instance
     */
    abstract protected function getModel();

    /**
     * Get the view path for the resource
     */
    abstract protected function getViewPath(): string;

    /**
     * Get the route name prefix
     */
    abstract protected function getRoutePrefix(): string;

    /**
     * Get the resource name (singular)
     */
    abstract protected function getResourceName(): string;

    /**
     * Get the resource name (plural)
     */
    abstract protected function getResourceNamePlural(): string;

    /**
     * Get additional data for create/edit forms
     */
    protected function getFormData(): array
    {
        return [];
    }

    /**
     * Get action buttons for the index view
     */
    protected function getActionButtons(): array
    {
        return [
            [
                'text' => translate("create") . " " . translate($this->getResourceName()),
                'class' => 'open-create-modal btn-sm btn-success',
                'attr' => [
                    'data-modal-link' => route($this->getRoutePrefix() . '.create'),
                    'data-modal-title' => translate("create") . " " . translate($this->getResourceName()),
                ]
            ],
        ];
    }

    /**
     * Display a listing of the resource
     */
    public function index(): View
    {
        return $this->renderIndexView();
    }

    /**
     * Show the form for creating a new resource
     */
    public function create(): View
    {
        $formData = $this->getFormData();
        return view($this->getViewPath() . '.editor', $formData);
    }

    /**
     * Store a newly created resource in storage
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $this->validateAndPrepareData($request);
            $this->getService()->create($data);
            return $this->return(200, translate("created_successfully"));
        } catch (\Exception $e) {
            return $this->return(500, translate("creation_failed"), [], [$e->getMessage()]);
        }
    }

    /**
     * Display the specified resource
     */
    public function show(int $id): View|JsonResponse
    {
        try {
            $data = $this->getService()->get($id);
            if (!$data) {
                return $this->return(404, translate("resource_not_found"));
            }
            return view($this->getViewPath() . '.details', ['data' => $data]);
        } catch (\Exception $e) {
            return $this->return(500, translate("retrieval_failed"), [], [$e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource
     */
    public function edit(int $id): View|JsonResponse
    {
        try {
            $row = $this->getService()->get($id);
            if (!$row) {
                return $this->return(404, translate("resource_not_found"));
            }

            $formData = $this->getFormData();
            $formData['row'] = $row;

            return view($this->getViewPath() . '.editor', $formData);
        } catch (\Exception $e) {
            return $this->return(500, translate("retrieval_failed"), [], [$e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $data = $this->validateAndPrepareData($request);
            $this->getService()->update($id, $data);
            return $this->return(200, translate("updated_successfully"));
        } catch (\Exception $e) {
            return $this->return(500, translate("update_failed"), [], [$e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->getService()->delete($id);
            return $this->return(200, translate("deleted_successfully"));
        } catch (\Exception $e) {
            return $this->return(500, translate("deletion_failed"), [], [$e->getMessage()]);
        }
    }

    /**
     * Restore the specified resource
     */
    public function restore(int $id): JsonResponse
    {
        try {
            $this->getService()->restore($id);
            return $this->return(200, translate("restored_successfully"));
        } catch (\Exception $e) {
            return $this->return(500, translate("restoration_failed"), [], [$e->getMessage()]);
        }
    }

    /**
     * Render the index view
     */
    protected function renderIndexView(): View
    {
        $title = translate($this->getResourceNamePlural());
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate($this->getResourceNamePlural())],
        ];
        $actionButtons = $this->getActionButtons();

        return view($this->getViewPath() . '.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    /**
     * Validate and prepare data for storage
     */
    protected function validateAndPrepareData(Request $request): array
    {
        return $request->all();
    }

    /**
     * Get middleware configuration
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.' . static::getResourceNamePlural(), only: ['index', 'show']),
            new Middleware('permission:create.' . static::getResourceNamePlural(), only: ['create', 'store']),
            new Middleware('permission:update.' . static::getResourceNamePlural(), only: ['edit', 'update']),
            new Middleware('permission:delete.' . static::getResourceNamePlural(), only: ['destroy']),
            new Middleware('permission:restore.' . static::getResourceNamePlural(), only: ['restore']),
        ];
    }

    /**
     * Get resource name plural for middleware
     */
    protected static function getResourceNamePlural(): string
    {
        return 'resources';
    }
}
