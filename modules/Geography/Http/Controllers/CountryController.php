<?php

namespace Modules\Geography\Http\Controllers;

use Modules\Geography\Services\CountryService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CountryController extends Controller
{
    protected $service;

    public function __construct(CountryService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $countries = $this->service->getDataTables();
        return view('landlord.geography.countries.index');
    }

    public function create()
    {
        return view('landlord.geography.countries.editor');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $this->service->create($data);
        return redirect()->route('geography.index');
    }

    public function show($id)
    {
        $country = $this->service->get($id);
        return view('geography::show', compact('country'));
    }

    public function edit($id)
    {
        $country = $this->service->get($id);
        return view('geography::edit', compact('country'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $this->service->update($id, $data);
        return redirect()->route('geography.index');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return redirect()->route('geography.index');
    }
}
