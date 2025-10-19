{* Admin Dashboard *}
<div class="container mt-5">
<h2>{$ADMIN_DASHBOARD_TITLE|escape}</h2>
    <div class="row row-cols-1 row-cols-md-4 g-4 mb-4">
        <div class="col d-flex">
            <div class="card admin-card mb-3 w-100 h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title">{$primary_label|escape} {$UNITS_TITLE|escape}</h5>
                    <p class="card-text display-6 mb-0">{$primary_count}</p>
                </div>
            </div>
        </div>
        <div class="col d-flex">
            <div class="card admin-card mb-3 w-100 h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title">{$secondary_label|escape} {$UNITS_TITLE|escape}</h5>
                    <p class="card-text display-6 mb-0">{$unit_count}</p>
                </div>
            </div>
        </div>
        <div class="col d-flex">
            <div class="card admin-card mb-3 w-100 h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title">{$USERS_TITLE|escape}</h5>
                    <p class="card-text display-6 mb-0">{$user_count}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card admin-card">
                <div class="card-body">
                    <h5 class="card-title">{$ADMIN_QUICK_ADD_USER_TITLE|escape}</h5>
                    {$user_add_msg nofilter}
                    <form method="post">
                        <div class="mb-2">
                            <label>{$USERNAME_TITLE|escape}</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>{$EMAIL_TITLE|escape}</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>{$PASSWORD_TITLE|escape}</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>{$CONFIRM_PASSWORD_TITLE|escape}</label>
                            <input type="password" name="password2" class="form-control" required>
                        </div>
                        <button type="submit" name="add_user" class="btn btn-success">{$ADMIN_ADD_USER_TITLE|escape}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
