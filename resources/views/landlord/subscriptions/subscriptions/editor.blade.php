<form action="{{ isset($row) ? route('landlord.subscriptions.update', $row->id) : route('landlord.subscriptions.store') }}"
    class="{{ isset($row) ? 'edit-form' : 'create-form' }}" method="POST">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="brand_id" class="form-label">@translate('brand') <span class="text-danger">*</span></label>
        <select name="brand_id" id="brand_id" class="form-control select2" required>
            <option value="">@translate('select_brand')</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}"
                    {{ isset($row) && $row->brand_id == $brand->id ? 'selected' : '' }}>
                    {{ $brand->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="user_id" class="form-label">@translate('user') <span class="text-danger">*</span></label>
        <select name="user_id" id="user_id" class="form-control select2" required>
            <option value="">@translate('select_user')</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}"
                    {{ isset($row) && $row->user_id == $user->id ? 'selected' : '' }}>
                    {{ $user->name }} ({{ $user->email }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="plan_id" class="form-label">@translate('plan') <span class="text-danger">*</span></label>
        <select name="plan_id" id="plan_id" class="form-control select2" required>
            <option value="">@translate('select_plan')</option>
            @foreach ($plans as $plan)
                <option value="{{ $plan->id }}"
                    {{ isset($row) && $row->plan_id == $plan->id ? 'selected' : '' }}>
                    {{ $plan->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="currency_id" class="form-label">@translate('currency') <span class="text-danger">*</span></label>
        <select name="currency_id" id="currency_id" class="form-control select2" required>
            <option value="">@translate('select_currency')</option>
            @foreach ($currencies as $currency)
                <option value="{{ $currency->id }}"
                    {{ isset($row) && $row->currency_id == $currency->id ? 'selected' : '' }}>
                    {{ $currency->code }} - {{ $currency->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="price" class="form-label">@translate('price') <span class="text-danger">*</span></label>
        <input type="number" step="0.01" name="price" id="price" class="form-control"
            value="{{ isset($row) ? $row->price : '' }}" required>
    </div>

    <div class="form-group">
        <label for="billing_cycle" class="form-label">@translate('billing_cycle')</label>
        <select name="billing_cycle" id="billing_cycle" class="form-control select2">
            @foreach ($billingCycleOptions as $cycle)
                <option value="{{ $cycle }}" {{ isset($row) && $row->billing_cycle == $cycle ? 'selected' : '' }}>
                    @translate($cycle)
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="user_count" class="form-label">@translate('user_count')</label>
        <input type="number" name="user_count" id="user_count" class="form-control"
            value="{{ isset($row) ? $row->user_count : 1 }}">
    </div>

    <div class="form-group">
        <label for="starts_at" class="form-label">@translate('starts_at')</label>
        <input type="datetime-local" name="starts_at" id="starts_at" class="form-control"
            value="{{ isset($row) && $row->starts_at ? $row->starts_at->format('Y-m-d\TH:i') : '' }}">
    </div>

    <div class="form-group">
        <label for="ends_at" class="form-label">@translate('ends_at')</label>
        <input type="datetime-local" name="ends_at" id="ends_at" class="form-control"
            value="{{ isset($row) && $row->ends_at ? $row->ends_at->format('Y-m-d\TH:i') : '' }}">
    </div>

    <div class="form-group">
        <label for="trial_ends_at" class="form-label">@translate('trial_ends_at')</label>
        <input type="datetime-local" name="trial_ends_at" id="trial_ends_at" class="form-control"
            value="{{ isset($row) && $row->trial_ends_at ? $row->trial_ends_at->format('Y-m-d\TH:i') : '' }}">
    </div>

    <div class="form-group">
        <label for="status" class="form-label">@translate('status')</label>
        <select name="status" id="status" class="form-control select2">
            @foreach ($statusOptions as $status)
                <option value="{{ $status }}" {{ isset($row) && $row->status == $status ? 'selected' : '' }}>
                    @translate($status)
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="auto_renew" class="form-label">@translate('auto_renew')</label>
        <select name="auto_renew" id="auto_renew" class="form-control select2">
            @foreach ($autoRenewOptions as $option)
                <option value="{{ $option }}" {{ isset($row) && $row->auto_renew == $option ? 'selected' : '' }}>
                    @translate($option)
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? translate('update') : translate('create') }}</button>
    </div>
</form>
