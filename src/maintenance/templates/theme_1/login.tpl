{* Login Template *}
<h2>{$LOGIN_TITLE|escape}</h2>
{if $error}
    <div class="alert alert-danger">{$error|escape}</div>
{/if}
<form method="post">
    <div class="mb-3">
        <label for="username" class="form-label">{$USERNAME_TITLE|escape}</label>
        <input type="text" name="username" id="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">{$PASSWORD_TITLE|escape}</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">{$LOGIN_BUTTON|escape}</button>
</form>
<div class="mt-3">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>{$MESSAGE_BOARD_TITLE}</h5>
                </div>
                <div class="card-body">
                    {$message_board|escape}
                </div>
            </div>
        </div>
    </div>
</div>