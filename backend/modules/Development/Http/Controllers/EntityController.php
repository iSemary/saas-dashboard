<?php

namespace Modules\Development\Http\Controllers;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Utilities\Entities\Entity;
use Modules\Utilities\Entities\Module;
use Modules\Utilities\Entities\ModuleEntity;

class EntityController extends ApiController implements HasMiddleware
{
    /**
     * Define the middleware for the controller
     *
     * @return array Array of middleware configurations
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.modules_flow', only: ['index', 'sync']),
        ];
    }

    /**
     * Display the entities management page
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $title = translate("entities");

        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => ($title)],
        ];

        $actionButtons = [
            [
                'text' => translate("sync_entities"),
                'icon' => '<i class="fas fa-sync"></i>',
                'class' => 'btn-sm btn-orange text-white sync-entities',
                'attr' => [
                    'data-route' => route('landlord.development.entities.sync'),
                    'data-method' => 'POST',
                ]
            ],
        ];


        $entities = Entity::orderBy("entity_name")->get();
        $modules = Module::with('moduleEntities')->orderBy("name")->get();

        // Get module entities grouped by module_id for easier access in the view
        $moduleEntitiesMap = ModuleEntity::all()->groupBy('module_id')
            ->map(function ($items) {
                return $items->pluck('entity_id')->toArray();
            })->toArray();

        return view('landlord.developments.flows.module-entities', compact(
            'breadcrumbs',
            'title',
            'entities',
            'actionButtons',
            'modules',
            'moduleEntitiesMap'
        ));
    }

    /**
     * Synchronize entities by scanning Models and Entities directories
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function sync()
    {
        // Scan app/Models directory
        $modelPaths = glob(app_path('Models') . '/*.php');

        // Scan all modules for Entities folders
        $modulesPaths = glob(base_path('modules/*/Entities/*.php'));
        $modulesLowerPaths = glob(base_path('modules/*/entities/*.php'));

        $allPaths = array_merge($modelPaths, $modulesPaths, $modulesLowerPaths);

        foreach ($allPaths as $path) {
            $entityName = basename($path, '.php');
            $relativePath = str_replace(base_path() . '/', '', $path);

            Entity::updateOrCreate(
                ['entity_name' => $entityName],
                [
                    'entity_path' => $relativePath,
                    'entity_name' => $entityName,
                ]
            );
        }

        return response()->json([
            'message' => translate('entities_synced_successfully'),
            'count' => count($allPaths)
        ]);
    }

    /**
     * Store or update module-entity relationships
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Get the entities data from the request
            $moduleEntities = $request->input('entities', []);

            // Begin transaction
            \DB::beginTransaction();

            // Loop through each module
            foreach ($moduleEntities as $moduleId => $entityIds) {
                // Delete existing relationships for this module
                ModuleEntity::where('module_id', $moduleId)->delete();

                // Create new relationships
                foreach ($entityIds as $entityId) {
                    ModuleEntity::create([
                        'module_id' => $moduleId,
                        'entity_id' => $entityId
                    ]);
                }
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => translate('module_entities_updated_successfully')
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => translate('error_updating_module_entities')
            ], 500);
        }
    }
}
