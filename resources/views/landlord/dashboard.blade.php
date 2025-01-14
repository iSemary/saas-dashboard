@extends('layouts.landlord.app')
@section("content")
{{-- {{ app(\Modules\Development\Services\ConfigurationService::class)->getByKey('site_name') }} --}}
{{ configuration('site_name') }}
@endsection