{* Admin Dashboard *}
<div class="container mt-5">
    <h2>Admin Dashboard</h2>
    <div class="row row-cols-1 row-cols-md-4 g-4 mb-4">
        <div class="col d-flex">
            <div class="card admin-card mb-3 w-100 h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title">{$primary_label|escape} Units</h5>
                    <p class="card-text display-6 mb-0">{$primary_count}</p>
                </div>
            </div>
        </div>
        <div class="col d-flex">
            <div class="card admin-card mb-3 w-100 h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title">{$secondary_label|escape} Units</h5>
                    <p class="card-text display-6 mb-0">{$unit_count}</p>
                </div>
            </div>
        </div>
        <div class="col d-flex">
            <div class="card admin-card mb-3 w-100 h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title">Users</h5>
                    <p class="card-text display-6 mb-0">{$user_count}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card admin-card">
                <div class="card-body">
                    <h5 class="card-title">Quick Add User</h5>
                    {$user_add_msg nofilter}
                    <form method="post">
                        <div class="mb-2">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Confirm Password</label>
                            <input type="password" name="password2" class="form-control" required>
                        </div>
                        <button type="submit" name="add_user" class="btn btn-success">Add User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
