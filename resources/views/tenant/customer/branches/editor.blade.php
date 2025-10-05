<form id="branchForm" class="form-horizontal">
    @csrf
    @if(isset($branch))
        @method('PUT')
    @endif

    <div class="row">
        <!-- Basic Information -->
        <div class="col-md-6">
            <div class="form-group">
                <label for="name" class="col-sm-3 control-label">@translate('name') <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="name" name="name" 
                           value="{{ $branch->name ?? '' }}" 
                           placeholder="@translate('enter_branch_name')" required>
                </div>
            </div>

            <div class="form-group">
                <label for="code" class="col-sm-3 control-label">@translate('code')</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="code" name="code" 
                           value="{{ $branch->code ?? '' }}" 
                           placeholder="@translate('enter_branch_code')" 
                           maxlength="10" style="text-transform: uppercase;">
                    <small class="form-text text-muted">@translate('leave_empty_to_auto_generate')</small>
                </div>
            </div>

            <div class="form-group">
                <label for="brand_id" class="col-sm-3 control-label">@translate('brand') <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <select class="form-control" id="brand_id" name="brand_id" required>
                        <option value="">@translate('select_brand')</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" 
                                    {{ (isset($branch) && $branch->brand_id == $brand->id) ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="status" class="col-sm-3 control-label">@translate('status') <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <select class="form-control" id="status" name="status" required>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" 
                                    {{ (isset($branch) && $branch->status == $value) ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="col-sm-3 control-label">@translate('description')</label>
                <div class="col-sm-9">
                    <textarea class="form-control" id="description" name="description" 
                              rows="3" placeholder="@translate('enter_branch_description')">{{ $branch->description ?? '' }}</textarea>
                </div>
            </div>
        </div>

        <!-- Working Hours and Days -->
        <div class="col-md-12">
            <h5 class="mb-3">@translate('working_hours_and_days')</h5>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-sm-3 control-label">@translate('working_days')</label>
                <div class="col-sm-9">
                    <div class="row">
                        @php
                            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                            $workingDays = isset($branch) && $branch->working_days ? $branch->working_days : [];
                        @endphp
                        @foreach($days as $day)
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input working-day-checkbox" 
                                           id="working_days_{{ $day }}" name="working_days[{{ $day }}]" value="1"
                                           {{ isset($workingDays[$day]) && $workingDays[$day] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="working_days_{{ $day }}">
                                        @translate(ucfirst($day))
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="col-sm-3 control-label">@translate('working_hours')</label>
                <div class="col-sm-9">
                    <div id="working-hours-container">
                        @php
                            $workingHours = isset($branch) && $branch->working_hours ? $branch->working_hours : [];
                        @endphp
                        @foreach($days as $day)
                            <div class="row mb-2 working-hours-row" id="hours_{{ $day }}" 
                                 style="{{ isset($workingDays[$day]) && $workingDays[$day] ? '' : 'display: none;' }}">
                                <div class="col-md-4">
                                    <label class="form-label">@translate(ucfirst($day))</label>
                                </div>
                                <div class="col-md-3">
                                    <input type="time" class="form-control" 
                                           name="working_hours[{{ $day }}][open]" 
                                           value="{{ isset($workingHours[$day]['open']) ? $workingHours[$day]['open'] : '' }}"
                                           placeholder="@translate('open_time')">
                                </div>
                                <div class="col-md-3">
                                    <input type="time" class="form-control" 
                                           name="working_hours[{{ $day }}][close]" 
                                           value="{{ isset($workingHours[$day]['close']) ? $workingHours[$day]['close'] : '' }}"
                                           placeholder="@translate('close_time')">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary close-day-btn" 
                                            data-day="{{ $day }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="col-md-6">
            <div class="form-group">
                <label for="phone" class="col-sm-3 control-label">@translate('phone')</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="phone" name="phone" 
                           value="{{ $branch->phone ?? '' }}" 
                           placeholder="@translate('enter_phone_number')">
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="col-sm-3 control-label">@translate('email')</label>
                <div class="col-sm-9">
                    <input type="email" class="form-control" id="email" name="email" 
                           value="{{ $branch->email ?? '' }}" 
                           placeholder="@translate('enter_email_address')">
                </div>
            </div>

            <div class="form-group">
                <label for="website" class="col-sm-3 control-label">@translate('website')</label>
                <div class="col-sm-9">
                    <input type="url" class="form-control" id="website" name="website" 
                           value="{{ $branch->website ?? '' }}" 
                           placeholder="@translate('enter_website_url')">
                </div>
            </div>

            <div class="form-group">
                <label for="manager_name" class="col-sm-3 control-label">@translate('manager_name')</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="manager_name" name="manager_name" 
                           value="{{ $branch->manager_name ?? '' }}" 
                           placeholder="@translate('enter_manager_name')">
                </div>
            </div>

            <div class="form-group">
                <label for="manager_email" class="col-sm-3 control-label">@translate('manager_email')</label>
                <div class="col-sm-9">
                    <input type="email" class="form-control" id="manager_email" name="manager_email" 
                           value="{{ $branch->manager_email ?? '' }}" 
                           placeholder="@translate('enter_manager_email')">
                </div>
            </div>

            <div class="form-group">
                <label for="manager_phone" class="col-sm-3 control-label">@translate('manager_phone')</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="manager_phone" name="manager_phone" 
                           value="{{ $branch->manager_phone ?? '' }}" 
                           placeholder="@translate('enter_manager_phone')">
                </div>
            </div>
        </div>
    </div>

    <!-- Address Information -->
    <div class="row">
        <div class="col-md-12">
            <h5 class="mb-3">@translate('address_information')</h5>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label for="address" class="col-sm-3 control-label">@translate('address')</label>
                <div class="col-sm-9">
                    <textarea class="form-control" id="address" name="address" 
                              rows="2" placeholder="@translate('enter_address')">{{ $branch->address ?? '' }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="city" class="col-sm-3 control-label">@translate('city')</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="city" name="city" 
                           value="{{ $branch->city ?? '' }}" 
                           placeholder="@translate('enter_city')">
                </div>
            </div>

            <div class="form-group">
                <label for="state" class="col-sm-3 control-label">@translate('state')</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="state" name="state" 
                           value="{{ $branch->state ?? '' }}" 
                           placeholder="@translate('enter_state')">
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="country" class="col-sm-3 control-label">@translate('country')</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="country" name="country" 
                           value="{{ $branch->country ?? '' }}" 
                           placeholder="@translate('enter_country')">
                </div>
            </div>

            <div class="form-group">
                <label for="postal_code" class="col-sm-3 control-label">@translate('postal_code')</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="postal_code" name="postal_code" 
                           value="{{ $branch->postal_code ?? '' }}" 
                           placeholder="@translate('enter_postal_code')">
                </div>
            </div>

            <div class="form-group">
                <label for="latitude" class="col-sm-3 control-label">@translate('latitude')</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" id="latitude" name="latitude" 
                           value="{{ $branch->latitude ?? '' }}" 
                           placeholder="@translate('enter_latitude')" 
                           step="any" min="-90" max="90">
                </div>
            </div>

            <div class="form-group">
                <label for="longitude" class="col-sm-3 control-label">@translate('longitude')</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" id="longitude" name="longitude" 
                           value="{{ $branch->longitude ?? '' }}" 
                           placeholder="@translate('enter_longitude')" 
                           step="any" min="-180" max="180">
                </div>
            </div>
        </div>
    </div>

    <div class="form-group text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">@translate('cancel')</button>
        <button type="submit" class="btn btn-primary">
            @if(isset($branch))
                @translate('update')
            @else
                @translate('create')
            @endif
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Auto-generate code from name
    $('#name').on('blur', function() {
        if (!$('#code').val()) {
            const name = $(this).val();
            const code = name.replace(/[^A-Za-z0-9]/g, '').substring(0, 3).toUpperCase();
            $('#code').val(code);
        }
    });

    // Convert code to uppercase
    $('#code').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });

    // Working days functionality
    $('.working-day-checkbox').on('change', function() {
        const day = $(this).attr('name').match(/\[(.*?)\]/)[1];
        const isChecked = $(this).is(':checked');
        
        if (isChecked) {
            $('#hours_' + day).show();
        } else {
            $('#hours_' + day).hide();
            // Clear the time inputs when unchecked
            $('#hours_' + day + ' input[type="time"]').val('');
        }
    });

    // Close day button functionality
    $('.close-day-btn').on('click', function() {
        const day = $(this).data('day');
        $('#working_days_' + day).prop('checked', false);
        $('#hours_' + day).hide();
        $('#hours_' + day + ' input[type="time"]').val('');
    });

    // Form submission
    $('#branchForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const url = @if(isset($branch))
            '{{ route("tenant.branches.update", $branch->id) }}'
        @else
            '{{ route("tenant.branches.store") }}'
        @endif;
        
        const method = @if(isset($branch)) 'PUT' @else 'POST' @endif;
        
        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: translate('success'),
                        text: response.message,
                        icon: 'success'
                    }).then(() => {
                        $('#table').DataTable().ajax.reload();
                        $('.modal').modal('hide');
                    });
                } else {
                    Swal.fire({
                        title: translate('error'),
                        text: response.message,
                        icon: 'error'
                    });
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    let errorMessage = '';
                    Object.keys(response.errors).forEach(key => {
                        errorMessage += response.errors[key].join('<br>') + '<br>';
                    });
                    Swal.fire({
                        title: translate('validation_error'),
                        html: errorMessage,
                        icon: 'error'
                    });
                } else {
                    Swal.fire({
                        title: translate('error'),
                        text: translate('something_went_wrong'),
                        icon: 'error'
                    });
                }
            }
        });
    });
});
</script>
