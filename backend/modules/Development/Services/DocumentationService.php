<?php

namespace Modules\Development\Services;

use Illuminate\Support\Facades\DB;
use Modules\Development\DTOs\CreateDocumentationData;
use Modules\Development\DTOs\UpdateDocumentationData;

class DocumentationService
{
    public function list(int $perPage = 50)
    {
        return DB::table('documentation')->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(CreateDocumentationData $data): array
    {
        $arrayData = [
            'title' => $data->title,
            'slug' => $data->slug,
            'body' => $data->body,
            'category' => $data->category,
            'is_published' => $data->is_published,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('documentation')->insert($arrayData);
        return $arrayData;
    }

    public function update($id, UpdateDocumentationData $data): array
    {
        $arrayData = array_merge($data->toArray(), ['updated_at' => now()]);
        DB::table('documentation')->where('id', $id)->update($arrayData);
        return $arrayData;
    }

    public function delete($id)
    {
        return DB::table('documentation')->where('id', $id)->delete();
    }
}
