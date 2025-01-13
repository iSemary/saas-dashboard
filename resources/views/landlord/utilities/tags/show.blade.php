<div class="card">
    <div class="card-header">
        @translate('tag_values')
    </div>
    <div class="card-body">
        <table id="showTable" data-route="{{ route('landlord.tags.show', $id) }}"
            class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">@translate('name')</th>
                    <th scope="col">@translate('slug')</th>
                    <th scope="col">@translate('description')</th>
                    <th scope="col">@translate('icon')</th>
                    <th scope="col">@translate('action')</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        @translate('create') @translate('tag_values')
    </div>
    <div class="card-body">
        <form action="{{ isset($row) ? route('landlord.tags.update', $row->id) : route('landlord.tags.store') }}"
            id="{{ isset($row) ? 'editForm' : 'createForm' }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if (isset($row))
                @method('PUT')
            @endif
            <input type="hidden" name="parent_id" value="{{ $id }}" required>
            <div class="form-group">
                <label for="name" class="form-label">@translate('name')</label>
                <input type="text" name="name" id="name" class="form-control"
                    value="{{ isset($row) ? $row->name : '' }}" required>
            </div>

            <div class="form-group">
                <label for="slug" class="form-label">@translate('slug')</label>
                <input type="text" name="slug" id="slug" class="form-control slug-input"
                    value="{{ isset($row) ? $row->slug : '' }}" required>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">@translate('description')</label>
                <textarea name="description" id="description" class="form-control">{{ isset($row) ? $row->description : '' }}</textarea>
            </div>

            <div class="form-group">
                <label for="icon" class="form-label">@translate('icon')</label><br />
                <input type="file" name="icon" id="icon" class="">
                @if (isset($row) && $row->icon)
                    <img src="{{ asset('path/to/icons/' . $row->icon) }}" alt="@translate('icon')" width="50">
                @endif
            </div>

            <div class="form-group">
                <label for="priority" class="form-label">@translate('priority')</label>
                <input type="number" name="priority" id="priority" class="form-control"
                    value="{{ isset($row) ? $row->priority : 0 }}">
            </div>

            <div class="form-group">
                <div class="form-status"></div>
            </div>

            <div class="form-group">
                <button type="submit"
                    class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? translate('update') : translate('create') }}</button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('assets/landlord/js/utilities/tags/show.js') }}"></script>
