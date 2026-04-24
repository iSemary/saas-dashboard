<?php

namespace Modules\Utilities\Repositories;

use App\Helpers\TableHelper;
use Modules\Utilities\Entities\StaticPage;
use Yajra\DataTables\DataTables;

class StaticPageRepository implements StaticPageInterface
{
    protected $model;

    public function __construct(StaticPage $staticPage)
    {
        $this->model = $staticPage;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function datatables()
    {
        $rows = $this->model->query()->withTrashed()->where(
            function ($q) {
                if (request()->from_date && request()->to_date) {
                    TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                }
            }
        );

        return DataTables::of($rows)
            ->editColumn('status', function ($row) {
                return translate($row->status);
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'landlord.static-pages.edit',
                    deleteRoute: 'landlord.static-pages.destroy',
                    restoreRoute: 'landlord.static-pages.restore',
                    type: $this->model->pluralTitle,
                    titleType: $this->model->singleTitle,
                    showIconsOnly: false
                );
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function find($id)
    {
        $row = $this->model->find($id);

        if ($row) {
            $row->load('attributes');
        }

        return $row;
    }

    public function create(array $data)
    {
        // Create the static page
        $staticPage = $this->model->create($data);

        // Handle attributes
        $this->handleAttributes($staticPage, $data);

        return $staticPage;
    }

    public function update($id, array $data)
    {
        $row = $this->model->find($id);
        if ($row) {
            // Update the static page
            $row->update($data);

            // Handle attributes
            $this->handleAttributes($row, $data);

            return $row;
        }
        return null;
    }

    /**
     * Handle attributes for a static page
     * 
     * @param Model $model The static page model
     * @param array $data Input data containing attributes
     */
    private function handleAttributes($model, array $data)
    {
        // Handle new format: attributes[key][language] = value
        if (isset($data['attributes']) && is_array($data['attributes'])) {
            foreach ($data['attributes'] as $attributeKey => $languages) {
                if (is_array($languages)) {
                    foreach ($languages as $languageCode => $attributeValue) {
                        // Only process if value is not empty
                        if (!empty($attributeValue)) {
                            $existingAttribute = $model->attributes()
                                ->where('key', $attributeKey)
                                ->where('language_code', $languageCode)
                                ->first();

                            if ($existingAttribute) {
                                // Update existing attribute
                                $existingAttribute->update([
                                    'value' => $attributeValue,
                                    'status' => 'active',
                                ]);
                            } else {
                                // Create new attribute
                                $model->attributes()->create([
                                    'key' => $attributeKey,
                                    'value' => $attributeValue,
                                    'language_code' => $languageCode,
                                    'status' => 'active',
                                ]);
                            }
                        }
                    }
                }
            }
        }

        // Handle old format: attribute_key, attribute_value arrays (fallback)
        if (isset($data['attribute_key']) && is_array($data['attribute_key'])) {
            foreach ($data['attribute_key'] as $key => $attributeKey) {
                // Check if value exists for this attribute
                $attributeValue = $data['attribute_value'][$key] ?? null;
                $attributeLanguage = $data['attribute_language'][$key] ?? 'en';
                $attributeStatus = $data['attribute_status'][$key] ?? 'active';

                // Only process if value is not null
                if ($attributeValue !== null) {
                    $existingAttribute = $model->attributes()
                        ->where('key', $attributeKey)
                        ->where('language_code', $attributeLanguage)
                        ->first();

                    if ($existingAttribute) {
                        // Update existing attribute
                        $existingAttribute->update([
                            'value' => $attributeValue,
                            'status' => $attributeStatus,
                        ]);
                    } else {
                        // Create new attribute
                        $model->attributes()->create([
                            'key' => $attributeKey,
                            'value' => $attributeValue,
                            'language_code' => $attributeLanguage,
                            'status' => $attributeStatus,
                        ]);
                    }
                }
            }
        }
    }

    public function delete($id)
    {
        $row = $this->model->find($id);
        if ($row) {
            $row->delete();

            $row->attributes()->delete();

            return true;
        }
        return false;
    }

    public function restore($id)
    {
        $row = $this->model->withTrashed()->find($id);
        if ($row) {
            $row->restore();
            return true;
        }
        return false;
    }
}
