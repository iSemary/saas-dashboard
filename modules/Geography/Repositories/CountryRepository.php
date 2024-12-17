<?php

namespace Modules\Geography\Repositories;

use Modules\Geography\Entities\Country;

class CountryRepository implements CountryInterface
{
    public function all()
    {
        return Country::all();
    }

    public function datatables()
    {
    }

    public function find($id)
    {
        return Country::find($id);
    }

    public function create(array $data)
    {
        return Country::create($data);
    }

    public function update($id, array $data)
    {
        $country = Country::find($id);
        if ($country) {
            $country->update($data);
            return $country;
        }
        return null;
    }

    public function delete($id)
    {
        $country = Country::find($id);
        if ($country) {
            $country->delete();
            return true;
        }
        return false;
    }
}
