<form action="{{ route('translations.object.update', $row->id) }}" class="{{ 'edit-form' }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="object_type" value="{{ $objectType }}" required>
    <input type="hidden" name="object_key" value="{{ $objectKey }}" required>
    <div class="accordion" id="accordionExample">
        @foreach ($languages as $index => $language)
            <div class="card">
                <div class="card-header" id="heading{{ $index }}">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left {{ $index == 0 ? '' : 'collapsed' }}"
                            type="button" data-toggle="collapse" data-target="#collapse{{ $index }}"
                            aria-expanded="{{ $index == 0 ? 'true' : 'false' }}"
                            aria-controls="collapse{{ $index }}">
                            {{ $language->name }}
                        </button>
                    </h2>
                </div>
                <div id="collapse{{ $index }}" class="collapse {{ $index == 0 ? 'show' : '' }}"
                    aria-labelledby="heading{{ $index }}" data-parent="#accordionExample">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="{{ $key }}_{{ $index }}"
                                class="form-label">{{ translate($key) }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="{{ $key }}[{{ $language->locale }}]"
                                id="{{ $key }}_{{ $index }}" class="form-control"
                                value="{{ $row->getTranslatable($key, $language->locale) }}" required>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-{{ 'primary' }}">{{ translate('update') }}</button>
    </div>
</form>
