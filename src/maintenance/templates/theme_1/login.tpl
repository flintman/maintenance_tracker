{* Login Template *}
<h2>Login</h2>
{if $error}
    <div class="alert alert-danger">{$error|escape}</div>
{/if}
<form method="post">
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" name="username" id="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
</form>
<div class="mt-3">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Message Board</h5>
                </div>
                <div class="card-body">
                    {$message_board|escape}
                </div>
            </div>
        </div>
    </div>
</div>