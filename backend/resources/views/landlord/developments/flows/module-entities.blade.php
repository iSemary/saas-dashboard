@extends('layouts.landlord.app')
@section('content')
    <div class="card">
        <div class="card-header">
            @translate('module_entities')
        </div>
        <div class="card-body">
            <form action="{{ route('landlord.development.entities.store') }}" class="{{ 'create-form' }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @foreach ($modules as $module)
                    <div class="form-group">
                        <label for="entity" class="form-label">{{ ucfirst($module->name) }}</label>
                        <select name="entities[{{ $module->id }}][]" class="form-control select2" required multiple>
                            @foreach ($entities as $entity)
                                <option value="{{ $entity->id }}" 
                                    {{ isset($moduleEntitiesMap[$module->id]) && in_array($entity->id, $moduleEntitiesMap[$module->id]) ? 'selected' : '' }}>
                                    {{ $entity->entity_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endforeach

                <div class="form-group">
                    <div class="form-status"></div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-{{ 'success' }}">{{ translate('Sync') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/landlord/js/developments/flows/module-entities.js') }}"></script>
@endsection
