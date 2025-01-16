<?php

namespace Modules\Localization\Repositories;

use App\Helpers\TableHelper;
use Modules\Localization\Entities\Language;
use Yajra\DataTables\DataTables;

class LanguageRepository implements LanguageInterface
{
    protected $model;

    public function __construct(Language $language)
    {
        $this->model = $language;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function datatables()
    {
        $rows = $this->model->query()
            ->withCount('translations')
            ->where(function ($q) {
                if (request()->from_date && request()->to_date) {
                    TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                }
            });

        $totalEnglishTranslations = $this->model->where('locale', 'en')->withCount('translations')->first()->translations_count;

        return DataTables::of($rows)
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    $row,
                    'landlord.languages.edit',
                    'landlord.languages.destroy',
                    $this->model->pluralTitle,
                    $this->model->singleTitle,
                );
            })
            ->addColumn('total_translations', function ($row) use ($totalEnglishTranslations) {
                return '<span class="' . ($totalEnglishTranslations == $row->translations_count ? 'text-success' : 'text-danger') . '">' .$row->translations_count . '</span>';
            })
            ->rawColumns(['actions', 'total_translations'])
            ->make(true);;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $row = $this->model->find($id);
        if ($row) {
            $row->update($data);
            return $row;
        }
        return null;
    }

    public function delete($id)
    {
        $row = $this->model->find($id);
        if ($row) {
            $row->delete();
            return true;
        }
        return false;
    }
}
