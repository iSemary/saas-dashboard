@extends('layouts.landlord.app')
@section("content")

{{ Modules\Utilities\Entities\Category::where('id', 19)->first()->icon }}

@endsection