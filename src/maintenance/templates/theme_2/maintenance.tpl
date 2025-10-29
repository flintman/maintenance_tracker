<style>
    [data-bs-theme="dark"] .modern-table thead,
    [data-bs-theme="dark"] .modern-table .table-light,
    [data-bs-theme="dark"] .modern-table th {
        background-color: #232526 !important;
        color: #e0e0e0 !important;
        border-color: #444 !important;
    }
    .modern-btn-info {
        background: linear-gradient(90deg, #232526 0%, #00bcd4 100%) !important;
        color: #fff !important;
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    }
    .modern-btn-warning {
        background: linear-gradient(90deg, #232526 0%, #ff9800 100%) !important;
        color: #fff !important;
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    }
    .modern-btn-secondary {
        background: linear-gradient(90deg, #232526 0%, #607d8b 100%) !important;
        color: #fff !important;
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    }
    .modern-btn-success {
        background: linear-gradient(90deg, #232526 0%, #4caf50 100%) !important;
        color: #fff !important;
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    }
</style>
<div class="container py-4">
    <h1 class="display-5 fw-bold mb-4">{$MAINTENANCE_FOR_TITLE|escape} {$equipment_name|escape}</h1>
    <a href="{if $secondary_id}units.php?secondary=1{else}units.php{/if}" class="btn btn-secondary mb-3">
        {$BACK_TO_LABEL|escape}
        {if $secondary_id}
            {$secondary_label|escape} {$UNITS_TITLE|escape}
        {else}
            {$primary_label|escape} {$UNITS_TITLE|escape}
        {/if}
    </a>

    {if isset($msg)}
        <div class="alert alert-success">{$msg|escape}</div>
    {/if}

    <div class="mb-4">
        <button class="btn modern-btn modern-btn-primary" type="button" id="toggleAddEditBtn" data-bs-toggle="collapse" data-bs-target="#addMaintenanceForm" aria-expanded="false" aria-controls="addMaintenanceForm" onclick="toggleArrow(this)">
            <span class="me-2 arrow" id="addEditArrow">➤</span> <span class="toggle-text" id="addEditText">{$MAINTENANCE_ADD_RECORD_TITLE|escape}</span>
        </button>
    </div>

    <div class="collapse" id="addMaintenanceForm">
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="secondary_id" value="{$secondary_id}">
            <input type="hidden" name="pmy_id" value="{$pmy_id}">
            <div class="card modern-card shadow-sm mb-4">
                <div class="card-header bg-gradient text-white fw-bold" style="background: linear-gradient(90deg, #232526 0%, #00bcd4 100%) !important;">{$MAINTENANCE_SERVICE_DETAILS|escape}</div>
                <div class="card-body">
                    <div class="row g-3 align-items-center mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="type_of_service">{$TYPE_OF_SERVICE_LABEL|escape}</label>
                            <input type="text" name="type_of_service" id="type_of_service" class="form-control" required placeholder="{$TYPE_OF_SERVICE_PLACEHOLDER|escape}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="performed_by">{$PERFORMED_BY_LABEL|escape}</label>
                            {if $is_admin}
                                <select name="performed_by" id="performed_by" class="form-control" required>
                                    <option value="">{$SELECT_USER_LABEL|escape}</option>
                                    {foreach $all_users as $user}
                                        <option value="{$user.username|escape}"{if $user.username == $current_user} selected{/if}>{$user.nickname|escape} ({$user.username|escape})</option>
                                    {/foreach}
                                </select>
                            {else}
                                <input type="text" name="performed_by" id="performed_by" class="form-control" value="{$current_nickname|escape}" readonly required>
                            {/if}
                        </div>
                    </div>
                    <div class="row g-3 align-items-center mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="performed_at">{$PERFORMED_ON_LABEL|escape}</label>
                            <input type="date" name="performed_at" id="performed_at" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="costs_of_parts">{$COSTS_OF_PARTS_LABEL|escape}</label>
                            <input type="number" name="costs_of_parts" id="costs_of_parts" class="form-control" step="0.01" value="0.00" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="description">{$DESCRIPTION_LABEL|escape}</label>
                        <textarea name="description" id="description" class="form-control" rows="3" required placeholder="{$DESCRIPTION_PLACEHOLDER|escape}"></textarea>
                    </div>
                </div>
            </div>
            <div class="card modern-card mb-4">
                <div class="card-header bg-secondary text-white fw-bold">{$PHOTOS_TITLE|escape}</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="photos">{$UPLOAD_PHOTOS_LABEL|escape}</label>
                        <input type="file" name="photos[]" id="photos" class="form-control" multiple>
                        <small class="form-text text-muted">{$PHOTOS_HINT|escape}</small>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end mb-3">
                <button class="btn modern-btn modern-btn-primary px-4" name="add_maintenance">{$BTN_SUBMIT|escape}</button>
            </div>
        </form>
    </div>

    <h3 class="mt-5 mb-3">{$MAINTENANCE_RECORDS_TITLE|escape}</h3>

    <div id="maintenanceTable" class="sortable-table">
                <div class="row mb-3">
            <div class="col-md-6">
                <input class="form-control search" placeholder="{$SEARCH_MAINTENANCE_PLACEHOLDER|escape}" />
            </div>
        </div>

        <div class="table-responsive">
            <table class="table modern-table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="sort" data-sort="record_id" style="cursor: pointer;">{$TH_ID|escape}</th>
                        <th class="sort" data-sort="service_type" style="cursor: pointer;">{$TH_TYPE|escape}</th>
                        <th class="sort" data-sort="description" style="cursor: pointer;">{$TH_DESCRIPTION|escape}</th>
                        <th>{$TH_ACTIONS|escape}</th>
                    </tr>
                </thead>
                <tbody class="list">
            {foreach $records as $row}
                <tr>
                    <td class="record_id">{$row.id}</td>
                    <td class="service_type">{$row.type_of_service|escape}</td>
                    <td class="description">{$row.description|escape}</td>
                    <td>
                        <a href="view_maintenance.php?id={$row.id}&type={$secondary_id ? 'secondary_units' : 'primary'}" class="btn modern-btn modern-btn-info btn-sm">{$BTN_VIEW|escape}</a>
                        {if $session.privilege == 'admin'}
                        <a href="view_maintenance.php?id={$row.id}&type={$secondary_id ? 'secondary_units' : 'primary'}&edit=1" class="btn modern-btn modern-btn-secondary btn-sm">{$EDIT_BUTTON|escape}</a>
                        {/if}
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        </div>

        <nav>
            <ul class="pagination"></ul>
        </nav>
    </div>
    {if $records|@count == 0}
    <div class="alert alert-secondary">{$NO_MAINTENANCE_RECORDS|escape}</div>
    {/if}
</div>
<script>
function toggleArrow(button) {
    const arrow = button.querySelector('.arrow');
    arrow.textContent = button.getAttribute('aria-expanded') === 'true' ? '▼' : '➤';
}

// Initialize List.js for maintenance records table
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('maintenanceTable')) {
        var maintenanceOptions = {
            valueNames: ['record_id', 'service_type', 'description'],
            pagination: true,
            page: 10,
            searchColumns: ['record_id', 'service_type', 'description']
        };
        var maintenanceList = new List('maintenanceTable', maintenanceOptions);
    }
});
</script>