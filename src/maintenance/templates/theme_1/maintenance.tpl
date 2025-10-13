{* Maintenance Records Template *}
<h2>Maintenance for {$equipment_name|escape}</h2>
<a href="{$refrigeration_id ? 'refrigeration.php' : 'trailer.php'}" class="btn btn-secondary mb-3">
    Back to {$refrigeration_id ? 'Refrigeration Units' : 'Trailers'}
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
        <input type="hidden" name="refrigeration_id" value="{$refrigeration_id}">
        <input type="hidden" name="trl_id" value="{$trl_id}">
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
                        <input type="text" name="performed_by" id="performed_by" class="form-control" required placeholder="Technician Name">
                    </div>
                </div>
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold" for="performed_at">Performed On</label>
                        <input type="date" name="performed_at" id="performed_at" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold" for="costs_of_parts">Costs of Parts</label>
                        <input type="number" name="costs_of_parts" id="costs_of_parts" class="form-control" step="0.01" required placeholder="0.00">
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
<table class="table table-bordered tablesorter">
    <thead><tr><th>ID</th><th>Type</th><th>Description</th><th>Actions</th></tr></thead>
    <tbody>
    {foreach $records as $row}
        <tr>
            <td>{$row.id}</td>
            <td>{$row.type_of_service|escape}</td>
            <td>{$row.description|escape}</td>
            <td>
                <a href="view_maintenance.php?id={$row.id}&type={$refrigeration_id ? 'refrigeration' : 'trailer'}" class="btn btn-info">View</a>
                {if $session.privilege == 'admin'}
                <a href="view_maintenance.php?id={$row.id}&type={$refrigeration_id ? 'refrigeration' : 'trailer'}&edit=1" class="btn btn-warning">Edit</a>
                {/if}
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
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
});
</script>
