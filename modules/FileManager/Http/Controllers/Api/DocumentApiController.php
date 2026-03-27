<?php

namespace Modules\FileManager\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Modules\FileManager\Entities\File;
use Modules\FileManager\Entities\Folder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentApiController extends ApiController
{
    public function index(Request $request)
    {
        try {
            $folderId = $request->get('folder_id');
            $search = $request->get('search');
            $perPage = $request->get('per_page', 20);

            $query = File::with('folder');

            if ($folderId) {
                $query->where('folder_id', $folderId);
            } else {
                $query->whereNull('folder_id');
            }

            if ($search) {
                $query->where('original_name', 'like', "%{$search}%");
            }

            $files = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'data' => [
                    'data' => $files->items(),
                    'current_page' => $files->currentPage(),
                    'last_page' => $files->lastPage(),
                    'per_page' => $files->perPage(),
                    'total' => $files->total(),
                    'from' => $files->firstItem(),
                    'to' => $files->lastItem(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve documents',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:10240', // 10MB max
                'folder_id' => 'nullable|exists:folders,id',
                'access_level' => 'nullable|in:private,public',
            ]);

            $file = $request->file('file');
            $folderId = $request->get('folder_id');
            $accessLevel = $request->get('access_level', 'public');

            // Generate hash name
            $hashName = $file->hashName();
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();
            $size = $file->getSize();
            $checksum = hash_file('sha256', $file->getRealPath());

            // Determine storage path
            $folderPath = $folderId ? Folder::find($folderId)->name : 'documents';
            $storagePath = $file->storeAs($folderPath, $hashName, 'public');

            // Create file record
            $document = File::create([
                'folder_id' => $folderId,
                'hash_name' => $hashName,
                'checksum' => $checksum,
                'original_name' => $originalName,
                'mime_type' => $mimeType,
                'host' => 'local',
                'status' => 'active',
                'access_level' => $accessLevel,
                'size' => $size,
                'metadata' => [
                    'uploaded_by' => auth()->id(),
                    'uploaded_at' => now()->toIso8601String(),
                ],
                'is_encrypted' => false,
            ]);

            return response()->json([
                'data' => $document->load('folder'),
                'message' => 'File uploaded successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to upload file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $file = File::with('folder')->findOrFail($id);
            return response()->json(['data' => $file]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'File not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $file = File::findOrFail($id);

            $validated = $request->validate([
                'original_name' => 'sometimes|string|max:255',
                'access_level' => 'sometimes|in:private,public',
                'status' => 'sometimes|in:active,inactive,archived',
                'folder_id' => 'nullable|exists:folders,id',
            ]);

            $file->update($validated);

            return response()->json([
                'data' => $file->load('folder'),
                'message' => 'File updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $file = File::findOrFail($id);
            
            // Delete physical file
            $folderPath = $file->folder ? $file->folder->name : 'documents';
            Storage::disk('public')->delete("{$folderPath}/{$file->hash_name}");
            
            $file->delete();

            return response()->json([
                'message' => 'File deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function download($id)
    {
        try {
            $file = File::findOrFail($id);
            
            // Check access permissions
            $user = auth()->user();
            if ($file->access_level === 'private' && $file->metadata && isset($file->metadata['uploaded_by'])) {
                // Only allow download if user uploaded it or has permission
                if ($file->metadata['uploaded_by'] != $user->id) {
                    // Check if user has permission to access private files
                    // This could be extended with role-based permissions
                    return response()->json([
                        'message' => 'You do not have permission to download this file'
                    ], 403);
                }
            }
            
            $folderPath = $file->folder ? $file->folder->name : 'documents';
            $filePath = "{$folderPath}/{$file->hash_name}";

            if (!Storage::disk('public')->exists($filePath)) {
                return response()->json([
                    'message' => 'File not found in storage'
                ], 404);
            }

            return Storage::disk('public')->download($filePath, $file->original_name);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to download file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function versions($id)
    {
        try {
            $file = File::findOrFail($id);
            // For now, return empty array. Version control can be implemented later
            return response()->json([
                'data' => []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve versions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:files,id'
            ]);

            $files = File::whereIn('id', $request->ids)->get();
            $deleted = 0;

            foreach ($files as $file) {
                $folderPath = $file->folder ? $file->folder->name : 'documents';
                Storage::disk('public')->delete("{$folderPath}/{$file->hash_name}");
                $file->delete();
                $deleted++;
            }

            return response()->json([
                'message' => "{$deleted} files deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete files',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
