<div class="{{ isset($classes) && !empty($classes) ? $classes : 'col-md-8' }}">
    <form method="GET" class="filter-table row" id="filterTable">
        <div class="form-group col-4">
            <label for=""><i class="fas fa-calendar-alt"></i> {{ translate('from_date') }}</label>
            <input class="form-control filter-table-from-date" name="from_date" id="from_date" type="datetime-local" value="" required />
        </div>
        <div class="form-group col-4">
            <label for=""><i class="far fa-calendar-alt"></i> @translate('to_date')</label>
            <input class="form-control filter-table-to-date" name="to_date" id="to_date" type="datetime-local" value="" required />
        </div>
        <div class="form-group col-4 mt-4">
            <button class="btn btn-info mt-2" type="submit"><i class="fas fa-filter"></i> @translate('filter_search')</button>
        </div>
    </form>
</div>