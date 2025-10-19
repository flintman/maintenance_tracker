{$refresh nofilter}
<div class="container py-4">
    <h1 class="display-5 fw-bold mb-4">{$USER_INFO_TITLE|escape}</h1>
    {if $errors}
        <div class="alert alert-danger">
            <ul class="mb-0">
            {foreach $errors as $error}
                <li>{$error|escape}</li>
            {/foreach}
            </ul>
        </div>
    {/if}
    {if $success_messages|@count gt 0}
        <div class="alert alert-success">
            <ul class="mb-0">
            {foreach $success_messages as $sm}
                <li>{$sm|escape}</li>
            {/foreach}
            </ul>
        </div>
    {elseif $success_message}
        <div class="alert alert-success">
            {$success_message|escape}
        </div>
    {/if}
    <div class="card modern-card mb-4">
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label class="form-label fw-bold">{$NICKNAME_LABEL|escape}</label><small class="form-text text-muted">{$NICKNAME_HINT|escape}</small>
                    <input type="text" name="nickname" class="form-control" value="{$user.nickname|escape}">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">{$EMAIL_TITLE|escape}</label>
                    <input type="email" name="email" class="form-control" value="{$user.email|escape}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">{$THEME_TITLE|escape}</label>
                    <select name="theme" class="form-control">
                        {foreach $themes as $theme}
                            <option value="{$theme|escape}"{if $user.theme == $theme} selected{/if}>{$theme|escape}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="mb-3">
                    <label>{$API_KEY_TITLE|escape}</label><small class="form-text text-muted">{$API_KEY_HINT|escape}</small>
                    <div class="input-group">
                        <input type="text" name="api_key" class="form-control" value="{$user.api_key|escape}" readonly>
                        <button class="btn btn-secondary" type="submit" name="generate_api_key" value="1">{$BTN_GENERATE_KEY|escape}</button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">{$CURRENT_PASSWORD_LABEL|escape}</label> <small class="form-text text-muted">{$CURRENT_PASSWORD_HINT|escape}</small>
                    <input type="password" name="current_password" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">{$NEW_PASSWORD_LABEL|escape}</label>
                    <input type="password" name="new_password" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">{$CONFIRM_NEW_PASSWORD_LABEL|escape}</label>
                    <input type="password" name="confirm_password" class="form-control">
                </div>
                <button class="btn modern-btn modern-btn-primary">{$BTN_SAVE|escape}</button>
            </form>
        </div>
    </div>
</div>
