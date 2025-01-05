@extends('layouts.landlord.app')
@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('landlord.development.code-builder.submit') }}" id="createForm" method="POST"
                enctype="multipart/form-data">
                @csrf
                
                <div class="row">

                    @foreach ($vars as $var)
                        <div class="form-group col-4">
                            <label for="name" class="form-label"><code>{{ $var }}</code></label>
                            <input type="text" name="{{ $var }}" id="{{ $var }}" class="form-control"
                                required>
                        </div>
                    @endforeach
                </div>

                <div class="form-group">
                    
                    <ul>
                        @foreach ($files as $file)
                            <li>{!! $file['name'] !!}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="form-group">
                    <div class="form-status"></div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">{{ translate('build') }}</button>
                </div>

            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/landlord/js/utilities/code-builder/index.js') }}"></script>
@endsection
