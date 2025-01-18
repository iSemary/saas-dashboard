@extends('layouts.landlord.app')
@section("content")

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>@translate('translations_status')</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped static-datatables table-hover dataTable no-footer">
                        <thead>
                        <tr>
                            <th>@translate('language')</th>
                            <th>@translate('total_translations')</th>
                            <th>@translate('total_without_objects')</th>
                            <th>@translate('total_with_objects')</th>
                            <th>@translate('total_shareable_translations')</th>
                            <th>@translate('total_json_translations')</th>
                            <th>@translate('total_datatable_translations')</th>
                            <th>@translate('status')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($languages as $language)
                            <tr>
                                <td>{{ $language->name }}</td>
                                <td>{{ $language->translations_count }}</td>
                                <td>{{ $language->total_without_objects }}</td>
                                <td>{{ $language->total_with_objects }}</td>
                                <td>{{ $language->shareable_translations_count }}</td>
                                <td>{{ $language->total_json_translations }}</td>
                                <td>{{ $language->total_datatable_translations }}</td>
                                <td>{{ $language->status  }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection