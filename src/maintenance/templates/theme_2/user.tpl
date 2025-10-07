<div class="container py-4">
    <h1 class="display-5 fw-bold mb-4">User Info</h1>
    {if $errors}
        <div class="alert alert-danger">
            <ul class="mb-0">
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
    <div class="card modern-card mb-4">
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" value="{$user.email|escape}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Current Password</label>
                    <input type="password" name="current_password" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">New Password</label>
                    <input type="password" name="new_password" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control">
                </div>
                <button class="btn modern-btn modern-btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
