<div class="container py-4">
    <h1 class="mb-4">{$ADMIN_CONFIGURATION_TITLE|escape}</h1>
    {if $msg}
        <div class="alert alert-success">{$msg}</div>
    {/if}
    <form method="post" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label for="default_theme" class="form-label fw-bold">{$DEFAULT_THEME_TITLE|escape}</label>
            <select name="default_theme" id="default_theme" class="form-control">
                {foreach $themes as $theme}
                    <option value="{$theme}" {if $config.default_theme == $theme}selected{/if}>{$theme}</option>
                {/foreach}
            </select>
        </div>
        <div class="mb-3">
            <label for="columns_to_show" class="form-label fw-bold">{$ADMIN_COLUMNS_TITLE|escape}</label>  <small class="form-text text-muted"> {$ADMIN_COLUMNS_HINT_TITLE|escape}</small>
            <input type="number" min="1" max="10" name="columns_to_show" id="columns_to_show" class="form-control" value="{$config.columns_to_show|escape}">
        </div>
        <div class="mb-3">
            <label for="primary_unit" class="form-label fw-bold">{$ADMIN_PRIMARY_TITLE|escape} </label>  <small class="form-text text-muted"> {$ADMIN_PRIMARY_HINT_TITLE|escape}</small>
            <input type="text" name="primary_unit" id="primary_unit" class="form-control" value="{$config.primary_unit|escape}">
        </div>
        <div class="mb-3">
            <label for="secondary_unit" class="form-label fw-bold">{$ADMIN_SECONDARY_TITLE|escape} </label>   <small class="form-text text-muted"> {$ADMIN_SECONDARY_HINT_TITLE|escape}</small>
            <input type="text" name="secondary_unit" id="secondary_unit" class="form-control" value="{$config.secondary_unit|escape}">
        </div>
    <button class="btn modern-btn modern-btn-primary px-4">{$BTN_SAVE|escape}</button>
    </form>
</div>