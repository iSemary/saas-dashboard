<form action="{{ isset($row) ? route('landlord.static-pages.update', $row->id) : route('landlord.static-pages.store') }}"
    class="{{ isset($row) ? 'edit-form' : 'create-form' }} sticky-form" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif
    <div class="form-content row">
        <div class="form-group col-6">
            <label for="name" class="form-label">@translate('name') <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control"
                value="{{ isset($row) ? $row->name : '' }}" required>
        </div>

        <div class="form-group col-6">
            <label for="slug" class="form-label">@translate('slug') <span class="text-danger">*</span></label>
            <input type="text" name="slug" id="slug" class="form-control slug-input"
                value="{{ isset($row) ? $row->slug : '' }}" required>
        </div>

        <div class="form-group col-12">
            <label for="description" class="form-label">@translate('description')</label>
            <textarea name="description" id="description" class="form-control">{{ isset($row) ? $row->description : '' }}</textarea>
        </div>

        <div class="form-group col-12">
            <label for="body" class="form-label">@translate('body')</label>
            <textarea name="body" id="ckInput" class="form-control ckeditor">{{ isset($row) ? $row->body : '' }}</textarea>
        </div>

        <div class="form-group col-6">
            <label for="status" class="form-label">@translate('status')</label>
            <select name="status" id="status" class="form-control select2">
                @foreach ($statusOptions as $status)
                    <option value="{{ $status }}" {{ isset($row) && $row->status == $status ? 'selected' : '' }}>
                        @translate($status)
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <hr />
    {{-- Attributes --}}
    <fieldset class="attribute-section">
        <legend>{{ translate('attributes') }}</legend>
        @foreach ($attributeKeys as $attributeKey)
            <div class="attribute-group mb-4">
                <input type="hidden" class="form-control" value="{{ $attributeKey }}"
                    name="attribute_key[{{ $attributeKey }}]" id="attribute-{{ $attributeKey }}" readonly />
                <div class="row">
                    <div class="form-group col-6">
                        <h5 class="attribute-title">{{ translate($attributeKey) }}</h5>
                        <input type="text" class="form-control" name="attribute_value[{{ $attributeKey }}]"
                            id="attribute-{{ $attributeKey }}"
                            value="{{ isset($row) ? $row->attributes()->where('attribute_key', $attributeKey)->value('attribute_value') : '' }}" />
                    </div>
                    <div class="form-group col-6">
                        <label for="status" class="form-label">@translate('status')</label>
                        <select name="attribute_status[{{ $attributeKey }}]" class="form-control select2">
                            @foreach ($attributeStatusOptions as $status)
                                <option value="{{ $status }}"
                                    {{ isset($row) && $row->attributes()->where('attribute_key', $attributeKey)->value('status') == $status ? 'selected' : '' }}>
                                    @translate($status)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if (!$loop->last)
                    <hr class="attribute-divider my-3">
                @endif
            </div>
        @endforeach
    </fieldset>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="sticky-footer">
        <div class="container-fluid">
            <div class="form-group mb-0">
                <button type="submit" class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">
                    {{ isset($row) ? translate('update') : translate('create') }}
                </button>
            </div>
        </div>
    </div>
</form>
