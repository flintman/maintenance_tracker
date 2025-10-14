{* Maintenance Records Template *}
<h2>Maintenance for {$equipment_name|escape}</h2>
<a href="{if $secondary_id}units.php?secondary=1{else}units.php{/if}" class="btn btn-secondary mb-3">
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

<h3>
    <button class="btn btn-link text-decoration-none" type="button" id="toggleAddMaintenanceBtn">
        <span class="me-2 arrow" id="addMaintenanceArrow">➤</span> <span class="toggle-text">Add Maintenance Record</span>
    </button>
</h3>

<div id="addMaintenanceForm" style="display:none;">
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="secondary_id" value="{$secondary_id}">
        <input type="hidden" name="pmy_id" value="{$pmy_id}">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white fw-bold">Service Details</div>
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
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-secondary text-white fw-bold">Photos</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold" for="photos">Upload Photos</label>
                    <input type="file" name="photos[]" id="photos" class="form-control" multiple>
                    <small class="form-text text-muted">You can select multiple images.</small>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <button class="btn btn-success px-4" name="add_maintenance">Submit</button>
        </div>
    </form>
</div>

<h3>Maintenance Records</h3>

<div id="maintenanceTable" class="sortable-table">
    <div class="row mb-3">
        <div class="col-md-6">
            <input class="form-control search" placeholder="Search maintenance records..." />
        </div>
    </div>

    <table class="table table-bordered modern-table">
        <thead>
            <tr>
                <th class="sort" data-sort="id" style="cursor: pointer;">ID</th>
                <th class="sort" data-sort="type" style="cursor: pointer;">Type</th>
                <th class="sort" data-sort="description" style="cursor: pointer;">Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="list">
        {foreach $records as $row}
            <tr>
                <td class="id">{$row.id}</td>
                <td class="type">{$row.type_of_service|escape}</td>
                <td class="description">{$row.description|escape}</td>
                <td>
                    <a href="view_maintenance.php?id={$row.id}&type={$secondary_id ? 'secondary_units' : 'primary'}" class="btn btn-info btn-sm">View</a>
                    {if $session.privilege == 'admin'}
                    <a href="view_maintenance.php?id={$row.id}&type={$secondary_id ? 'secondary_units' : 'primary'}&edit=1" class="btn btn-warning btn-sm">Edit</a>
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>

    <div class="row mt-3">
        <div class="col-md-6">
            <ul class="pagination"></ul>
        </div>
        <div class="col-md-6 text-end">
            <span class="list-info text-muted"></span>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var formDiv = document.getElementById('addMaintenanceForm');
    var arrow = document.getElementById('addMaintenanceArrow');
    var btn = document.getElementById('toggleAddMaintenanceBtn');
    if (btn && formDiv && arrow) {
        btn.addEventListener('click', function() {
            if (formDiv.style.display === 'none' || formDiv.style.display === '') {
                formDiv.style.display = 'block';
                arrow.textContent = '▼';
            } else {
                formDiv.style.display = 'none';
                arrow.textContent = '➤';
            }
        });
    }

    // Initialize List.js for maintenance table
    if (document.getElementById('maintenanceTable')) {
        var maintenanceList = new List('maintenanceTable', {
            valueNames: ['id', 'type', 'description'],
            pagination: true,
            page: 10,
            searchClass: 'search',
            listClass: 'list'
        });
    }
});
</script>
