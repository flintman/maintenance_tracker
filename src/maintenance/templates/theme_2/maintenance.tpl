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
    <h1 class="display-5 fw-bold mb-4">Maintenance for {$equipment_name|escape}</h1>
    <a href="{if $secondary_id}secondary.php{else}primary.php{/if}" class="btn btn-secondary mb-3">
        Back to
        {if $secondary_id}
            {$secondary_label|escape} Units
        {else}
            {$primary_label|escape} Units
        {/if}
    </a>

    {if isset($msg)}
        <div class="alert alert-success">{$msg|escape}</div>
    {/if}

    <div class="mb-4">
        <button class="btn modern-btn modern-btn-primary" type="button" id="toggleAddEditBtn" data-bs-toggle="collapse" data-bs-target="#addMaintenanceForm" aria-expanded="false" aria-controls="addMaintenanceForm" onclick="toggleArrow(this)">
            <span class="me-2 arrow" id="addEditArrow">➤</span> <span class="toggle-text" id="addEditText">Add Maintenance Record</span>
        </button>
    </div>

    <div class="collapse" id="addMaintenanceForm">
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="secondary_id" value="{$secondary_id}">
            <input type="hidden" name="pmy_id" value="{$pmy_id}">
            <div class="card modern-card shadow-sm mb-4">
                <div class="card-header bg-gradient text-white fw-bold" style="background: linear-gradient(90deg, #232526 0%, #00bcd4 100%) !important;">Service Details</div>
                <div class="card-body">
                    <div class="row g-3 align-items-center mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="type_of_service">Type of Service</label>
                            <input type="text" name="type_of_service" id="type_of_service" class="form-control" required placeholder="e.g. Inspection, Repair">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="performed_by">Performed By</label>
                            <input type="text" name="performed_by" id="performed_by" class="form-control" value="{$session.nickname|escape}" required>
                        </div>
                    </div>
                    <div class="row g-3 align-items-center mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="performed_at">Performed On</label>
                            <input type="date" name="performed_at" id="performed_at" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="costs_of_parts">Costs of Parts</label>
                            <input type="number" name="costs_of_parts" id="costs_of_parts" class="form-control" step="0.01" value="0.00" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="description">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="3" required placeholder="Describe the maintenance performed..."></textarea>
                    </div>
                </div>
            </div>
            <div class="card modern-card mb-4">
                <div class="card-header bg-secondary text-white fw-bold">Photos</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="photos">Upload Photos</label>
                        <input type="file" name="photos[]" id="photos" class="form-control" multiple>
                        <small class="form-text text-muted">You can select multiple images.</small>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end mb-3">
                <button class="btn modern-btn modern-btn-primary px-4" name="add_maintenance">Submit</button>
            </div>
        </form>
    </div>

    <h3 class="mt-5 mb-3">Maintenance Records</h3>
    <div class="table-responsive">
        <table class="table modern-table table-hover align-middle tablesorter">
            <thead class="table-light">
                <tr><th>ID</th><th>Type</th><th>Description</th><th>Actions</th></tr>
            </thead>
            <tbody>
            {foreach $records as $row}
                <tr>
                    <td>{$row.id}</td>
                    <td>{$row.type_of_service|escape}</td>
                    <td>{$row.description|escape}</td>
                    <td>
                        <a href="view_maintenance.php?id={$row.id}&type={$secondary_id ? 'secondary_units' : 'primary'}" class="btn modern-btn modern-btn-info btn-sm">View</a>
                        {if $session.privilege == 'admin'}
                        <a href="view_maintenance.php?id={$row.id}&type={$secondary_id ? 'secondary_units' : 'primary'}&edit=1" class="btn modern-btn modern-btn-secondary btn-sm">Edit</a>
                        {/if}
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    {if $records|@count == 0}
    <div class="alert alert-secondary">No maintenance records found.</div>
    {/if}
</div>
<script>
function toggleArrow(button) {
    const arrow = button.querySelector('.arrow');
    arrow.textContent = button.getAttribute('aria-expanded') === 'true' ? '▼' : '➤';
}
</script>