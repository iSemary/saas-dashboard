@extends('layouts.landlord.app')
@section("content")

    {{ auth()->user()->getLocale() }}

@endsection