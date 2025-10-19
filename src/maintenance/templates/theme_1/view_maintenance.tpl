{* View/Edit Maintenance Details *}
{if $alert}
    {$alert nofilter}
{/if}
<h2>{if $edit_mode}{$ADMIN_EDIT_BUTTON|escape}{else}{$BTN_VIEW|escape}{/if} {$MAINTENANCE_DETAILS_TITLE|escape}</h2>
<a href="{$backLink}" class="btn btn-secondary mb-3">{$BACK_TO_LABEL|escape} {$backLabel}</a>

{if $edit_mode}
<form method="post">
<table class="table table-bordered ">
    <tr><th>{$TYPE_OF_SERVICE_LABEL|escape}</th><td><input type="text" name="type_of_service" value="{$maintenance.type_of_service|escape}" class="form-control"></td></tr>
    <tr><th>{$DESCRIPTION_LABEL|escape}</th><td><textarea name="description" class="form-control">{$maintenance.description|escape}</textarea></td></tr>
    <tr><th>{$COSTS_OF_PARTS_LABEL|escape}</th><td><input type="number" name="costs_of_parts" value="{$maintenance.costs_of_parts|escape}" class="form-control"></td></tr>
    <tr><th>{$PERFORMED_ON_LABEL|escape}</th><td><input type="text" name="performed_at" value="{$maintenance.performed_at|escape}" class="form-control"></td></tr>
    <tr><th>{$PERFORMED_BY_LABEL|escape}</th><td><input type="text" name="performed_by" value="{$maintenance.performed_by|escape}" class="form-control"></td></tr>
</table>
<button type="submit" name="update" class="btn btn-primary">{$BTN_UPDATE_MAINTENANCE|escape}</button>
</form>
{else}
<table class="table table-bordered ">
    <tr><th>{$TYPE_OF_SERVICE_LABEL|escape}</th><td>{$maintenance.type_of_service|escape}</td></tr>
    <tr><th>{$DESCRIPTION_LABEL|escape}</th><td>{$maintenance.description|escape}</td></tr>
    <tr><th>{$COSTS_OF_PARTS_LABEL|escape}</th><td>${$maintenance.costs_of_parts|string_format:"%.2f"}</td></tr>
    <tr><th>{$PERFORMED_ON_LABEL|escape}</th><td>{$maintenance.performed_at|escape}</td></tr>
    <tr><th>{$PERFORMED_BY_LABEL|escape}</th><td>{$maintenance.performed_by|escape}</td></tr>
</table>
{/if}

<h3>{$PHOTOS_TITLE|escape}</h3>
{if $edit_mode}
<form method="post" enctype="multipart/form-data">
    <input type="file" name="photo" accept="image/*">
    <button type="submit" class="btn btn-success">{$BTN_ADD_PHOTO|escape}</button>
</form>
{/if}
<div class="photo-gallery">
    {foreach $photos as $photo}
        <div style="display:inline-block; margin:10px;">
            <img src="assets/uploads/{$photo|escape}" style="max-width:200px; display:block;">
            {if $edit_mode}
            <form method="post" style="display:inline;">
                <input type="hidden" name="delete_photo" value="{$photo|escape}">
                <button type="submit" class="btn btn-danger btn-sm">{$BTN_DELETE|escape}</button>
            </form>
            {/if}
        </div>
    {/foreach}
</div>
