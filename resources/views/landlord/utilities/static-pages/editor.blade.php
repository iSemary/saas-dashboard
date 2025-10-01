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

        <div class="form-group col-6">
            <label for="type" class="form-label">@translate('type')</label>
            <select name="type" id="type" class="form-control select2">
                <option value="page" {{ isset($row) && $row->type == 'page' ? 'selected' : '' }}>@translate('page')</option>
                <option value="policy" {{ isset($row) && $row->type == 'policy' ? 'selected' : '' }}>@translate('policy')</option>
                <option value="about_us" {{ isset($row) && $row->type == 'about_us' ? 'selected' : '' }}>@translate('about_us')</option>
                <option value="landing_page" {{ isset($row) && $row->type == 'landing_page' ? 'selected' : '' }}>@translate('landing_page')</option>
                <option value="blog" {{ isset($row) && $row->type == 'blog' ? 'selected' : '' }}>@translate('blog')</option>
            </select>
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

        <div class="form-group col-12">
            <label for="description" class="form-label">@translate('description')</label>
            <textarea name="description" id="description" class="form-control">{{ isset($row) ? $row->description : '' }}</textarea>
        </div>

        <div class="form-group col-6">
            <label for="meta_title" class="form-label">@translate('meta_title')</label>
            <input type="text" name="meta_title" id="meta_title" class="form-control"
                value="{{ isset($row) ? $row->meta_title : '' }}">
        </div>

        <div class="form-group col-6">
            <label for="meta_keywords" class="form-label">@translate('meta_keywords')</label>
            <input type="text" name="meta_keywords" id="meta_keywords" class="form-control"
                value="{{ isset($row) ? $row->meta_keywords : '' }}">
        </div>

        <div class="form-group col-12">
            <label for="meta_description" class="form-label">@translate('meta_description')</label>
            <textarea name="meta_description" id="meta_description" class="form-control">{{ isset($row) ? $row->meta_description : '' }}</textarea>
        </div>

        <div class="form-group col-6">
            <label for="is_public" class="form-label">@translate('is_public')</label>
            <select name="is_public" id="is_public" class="form-control select2">
                <option value="1" {{ isset($row) && $row->is_public ? 'selected' : '' }}>@translate('yes')</option>
                <option value="0" {{ isset($row) && !$row->is_public ? 'selected' : '' }}>@translate('no')</option>
            </select>
        </div>

        <div class="form-group col-6">
            <label for="order" class="form-label">@translate('order')</label>
            <input type="number" name="order" id="order" class="form-control"
                value="{{ isset($row) ? $row->order : 0 }}" min="0">
        </div>
    </div>
    <hr />
    {{-- Multi-language Content --}}
    <fieldset class="attribute-section">
        <legend>{{ translate('multi_language_content') }}</legend>
        
        {{-- Language Tabs --}}
        <ul class="nav nav-tabs" id="languageTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en" type="button" role="tab">
                    🇺🇸 English
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ar-tab" data-bs-toggle="tab" data-bs-target="#ar" type="button" role="tab">
                    🇸🇦 العربية
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="fr-tab" data-bs-toggle="tab" data-bs-target="#fr" type="button" role="tab">
                    🇫🇷 Français
                </button>
            </li>
        </ul>

        {{-- Language Content --}}
        <div class="tab-content" id="languageTabContent">
            {{-- English Content --}}
            <div class="tab-pane fade show active" id="en" role="tabpanel">
                <div class="row mt-3">
                    <div class="form-group col-12">
                        <label for="content_en" class="form-label">@translate('content') (English)</label>
                        <textarea name="attributes[content][en]" id="content_en" class="form-control ckeditor">
                            {{ isset($row) ? $row->getAttributeValue('content', 'en') : '' }}
                        </textarea>
                    </div>
                    <div class="form-group col-6">
                        <label for="title_en" class="form-label">@translate('title') (English)</label>
                        <input type="text" name="attributes[title][en]" id="title_en" class="form-control"
                            value="{{ isset($row) ? $row->getAttributeValue('title', 'en') : '' }}">
                    </div>
                    <div class="form-group col-6">
                        <label for="subtitle_en" class="form-label">@translate('subtitle') (English)</label>
                        <input type="text" name="attributes[subtitle][en]" id="subtitle_en" class="form-control"
                            value="{{ isset($row) ? $row->getAttributeValue('subtitle', 'en') : '' }}">
                    </div>
                </div>
            </div>

            {{-- Arabic Content --}}
            <div class="tab-pane fade" id="ar" role="tabpanel">
                <div class="row mt-3">
                    <div class="form-group col-12">
                        <label for="content_ar" class="form-label">@translate('content') (العربية)</label>
                        <textarea name="attributes[content][ar]" id="content_ar" class="form-control ckeditor" dir="rtl">
                            {{ isset($row) ? $row->getAttributeValue('content', 'ar') : '' }}
                        </textarea>
                    </div>
                    <div class="form-group col-6">
                        <label for="title_ar" class="form-label">@translate('title') (العربية)</label>
                        <input type="text" name="attributes[title][ar]" id="title_ar" class="form-control" dir="rtl"
                            value="{{ isset($row) ? $row->getAttributeValue('title', 'ar') : '' }}">
                    </div>
                    <div class="form-group col-6">
                        <label for="subtitle_ar" class="form-label">@translate('subtitle') (العربية)</label>
                        <input type="text" name="attributes[subtitle][ar]" id="subtitle_ar" class="form-control" dir="rtl"
                            value="{{ isset($row) ? $row->getAttributeValue('subtitle', 'ar') : '' }}">
                    </div>
                </div>
            </div>

            {{-- French Content --}}
            <div class="tab-pane fade" id="fr" role="tabpanel">
                <div class="row mt-3">
                    <div class="form-group col-12">
                        <label for="content_fr" class="form-label">@translate('content') (Français)</label>
                        <textarea name="attributes[content][fr]" id="content_fr" class="form-control ckeditor">
                            {{ isset($row) ? $row->getAttributeValue('content', 'fr') : '' }}
                        </textarea>
                    </div>
                    <div class="form-group col-6">
                        <label for="title_fr" class="form-label">@translate('title') (Français)</label>
                        <input type="text" name="attributes[title][fr]" id="title_fr" class="form-control"
                            value="{{ isset($row) ? $row->getAttributeValue('title', 'fr') : '' }}">
                    </div>
                    <div class="form-group col-6">
                        <label for="subtitle_fr" class="form-label">@translate('subtitle') (Français)</label>
                        <input type="text" name="attributes[subtitle][fr]" id="subtitle_fr" class="form-control"
                            value="{{ isset($row) ? $row->getAttributeValue('subtitle', 'fr') : '' }}">
                    </div>
                </div>
            </div>
        </div>
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
