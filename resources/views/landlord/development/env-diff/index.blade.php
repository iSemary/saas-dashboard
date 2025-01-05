@extends('layouts.landlord.app')
@section('content')
    <div class="card">
        <div class="card-body">
            @if ($data['status'] === 'success')
                <h5 class="card-title text-success">Success</h5>
                <p class="card-text">{{ $data['message'] }}</p>
                <p><strong>Total keys:</strong> {{ $data['env_count'] }}</p>
            @else
                <h5 class="card-title text-danger">Error</h5>
                <p class="card-text">{{ $data['message'] }}</p>
                <p><strong>Keys in .env:</strong> {{ $data['env_count'] }}</p>
                <p><strong>Keys in .env.example:</strong> {{ $data['env_example_count'] }}</p>

                <div class="mt-4">
                    <h6>Missing in .env:</h6>
                    <ul class="list-group mb-3">
                        @foreach ($data['missing_in_env'] as $key)
                            <li class="list-group-item">{{ $key }}</li>
                        @endforeach
                    </ul>
                    <h6>Missing in .env.example:</h6>
                    <ul class="list-group">
                        @foreach ($data['missing_in_env_example'] as $key)
                            <li class="list-group-item">{{ $key }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
@endsection
