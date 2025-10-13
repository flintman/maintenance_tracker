<div class="container-login">
    <div class="row g-4 w-100" style="max-width: 900px;">
        {if $message_board}
        <div class="col-md-6">
            <div class="message-banner h-100">
                <div class="alert alert-info h-100 d-flex flex-column">
                    <div>
                        <i class="fas fa-bullhorn me-2"></i>
                        <strong>System Announcement:</strong>
                    </div>
                    <div class="mt-2 flex-grow-1">
                        {$message_board|escape|nl2br}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
        {else}
        <div class="col-12 d-flex justify-content-center">
        {/if}
            <div class="login-card w-100">
                <h1 class="login-title mb-4">Login</h1>
                {if $error}<div class="alert alert-danger">{$error|escape}</div>{/if}
                <form method="post">
                    <div class="mb-3">
                        <input type="text" class="form-control" name="username" placeholder="Username" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn modern-btn-primary w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>
