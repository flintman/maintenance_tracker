<div class="container py-4">
    {if $alert}
        {$alert nofilter}
    {/if}
    <h1 class="display-5 fw-bold mb-4">{if $edit_mode}Edit{else}View{/if} Maintenance Details</h1>
    <a href="{$backLink}" class="btn modern-btn btn-secondary mb-3">Back to {$backLabel}</a>

    {if $edit_mode}
    <form method="post">
    <div class="card modern-card mb-4">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Type of Service</label>
                    <input type="text" name="type_of_service" value="{$maintenance.type_of_service|escape}" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Performed By</label>
                    <input type="text" name="performed_by" value="{$maintenance.performed_by|escape}" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Performed At</label>
                    <input type="text" name="performed_at" value="{$maintenance.performed_at|escape}" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Costs of Parts</label>
                    <input type="number" name="costs_of_parts" value="{$maintenance.costs_of_parts|escape}" class="form-control">
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <textarea name="description" class="form-control">{$maintenance.description|escape}</textarea>
                </div>
            </div>
            <button type="submit" name="update" class="btn modern-btn modern-btn-primary">Update Maintenance</button>
        </div>
    </div>
    </form>
    {else}
    <div class="card modern-card mb-4">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Type of Service</dt>
                <dd class="col-sm-9">{$maintenance.type_of_service|escape}</dd>
                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9">{$maintenance.description|escape}</dd>
                <dt class="col-sm-3">Costs of Parts</dt>
                <dd class="col-sm-9">${$maintenance.costs_of_parts|string_format:"%.2f"}</dd>
                <dt class="col-sm-3">Performed At</dt>
                <dd class="col-sm-9">{$maintenance.performed_at|escape}</dd>
                <dt class="col-sm-3">Performed By</dt>
                <dd class="col-sm-9">{$maintenance.performed_by|escape}</dd>
            </dl>
        </div>
    </div>
    {/if}

    <h3>Photos</h3>
    {if $edit_mode}
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="photo" accept="image/*">
        <button type="submit" class="btn modern-btn btn-success">Add Photo</button>
    </form>
    {/if}
    <div class="photo-gallery">
        {foreach $photos as $photo}
            <div style="display:inline-block; margin:10px;">
                <img src="assets/uploads/{$photo|escape}" style="max-width:200px; display:block;">
                {if $edit_mode}
                <form method="post" style="display:inline;">
                    <input type="hidden" name="delete_photo" value="{$photo|escape}">
                    <button type="submit" class="btn modern-btn btn-danger btn-sm">Delete</button>
                </form>
                {/if}
            </div>
        {/foreach}
    </div>
</div>
