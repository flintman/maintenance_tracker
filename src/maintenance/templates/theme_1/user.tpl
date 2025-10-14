{* User Info Page *}
{$refresh nofilter}
<h2>User Info</h2>
{if $errors}
    <div class="alert alert-danger">
        <ul>
        {foreach $errors as $error}
            <li>{$error|escape}</li>
        {/foreach}
        </ul>
    </div>
{/if}
{if $success_message}
    <div class="alert alert-success">
        {$success_message|escape}
    </div>
{/if}

<form method="post">
    <div class="mb-3">
        <label>Nickname </label> <small class="form-text text-muted"> Enter your nickname here,  will be displayed on your maintenance records</small>
        <input type="text" name="nickname" class="form-control" value="{$user.nickname|escape}">
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="{$user.email|escape}">
    </div>
    <div class="mb-3">
        <label>Theme</label>
        <select name="theme" class="form-control">
            {foreach $themes as $theme}
                <option value="{$theme|escape}"{if $user.theme == $theme} selected{/if}>{$theme|escape}</option>
            {/foreach}
        </select>
    </div>
    <div class="mb-3">
        <label>API Key </label> <small class="form-text text-muted"> Keep your API key secret. Generating a new key will invalidate the old one.</small>
        <div class="input-group">
            <input type="text" name="api_key" class="form-control" value="{$user.api_key|escape}" readonly>
            <button class="btn btn-secondary" type="submit" name="generate_api_key" value="1">Generate New</button>
        </div>
    </div>
    <div class="mb-3">
        <label>Current Password </label> <small class="form-text text-muted"> Enter in your current password to change your password.</small>
        <input type="password" name="current_password" class="form-control">
    </div>
    <div class="mb-3">
        <label>New Password</label>
        <input type="password" name="new_password" class="form-control">
    </div>
    <div class="mb-3">
        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" class="form-control">
    </div>
    <button class="btn btn-primary">Update</button>
</form>
