<?php

namespace Modules\FileManager\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Modules\FileManager\Entities\Folder;

class FolderApiController extends ApiController
{
    public function index(Request $request)
    {
        try {
            $parentId = $request->get('parent_id');
            $search = $request->get('search');

            $query = Folder::with(['files', 'parent']);

            if ($parentId !== null) {
                $query->where('parent_id', $parentId);
            } else {
                $query->whereNull('parent_id');
            }

            if ($search) {
                $query->where('name', 'like', "%{$search}%");
            }

            $folders = $query->orderBy('name')->get();

            return response()->json([
                'data' => $folders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve folders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:folders,id',
                'status' => 'nullable|in:active,inactive',
            ]);

            $validated['status'] = $validated['status'] ?? 'active';

            $folder = Folder::create($validated);

            return response()->json([
                'data' => $folder->load(['files', 'parent']),
                'message' => 'Folder created successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create folder',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $folder = Folder::with(['files', 'parent'])->findOrFail($id);
            return response()->json(['data' => $folder]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Folder not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $folder = Folder::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:folders,id',
                'status' => 'nullable|in:active,inactive',
            ]);

            $folder->update($validated);

            return response()->json([
                'data' => $folder->load(['files', 'parent']),
                'message' => 'Folder updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update folder',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $folder = Folder::findOrFail($id);
            
            // Check if folder has files or subfolders
            if ($folder->files()->count() > 0) {
                return response()->json([
                    'message' => 'Cannot delete folder with files'
                ], 400);
            }

            if (Folder::where('parent_id', $id)->count() > 0) {
                return response()->json([
                    'message' => 'Cannot delete folder with subfolders'
                ], 400);
            }

            $folder->delete();

            return response()->json([
                'message' => 'Folder deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete folder',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
