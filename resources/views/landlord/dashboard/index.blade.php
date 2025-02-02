@extends('layouts.landlord.app')
@section('content')

{{ auth()->user()->can('read.system_users') ? "Yes":"No" }}
{{ auth()->user()->roles }}
@endsection
