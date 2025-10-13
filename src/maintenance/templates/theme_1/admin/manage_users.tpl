<div class="container mt-5">
    <h2>Manage Users</h2>
    {$edit_msg nofilter}{$delete_msg nofilter}
    <h3 class="mt-4">All Users</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Nickname</th>
                <th>Privilege</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        {foreach $users as $user}
            <tr>
                <form method="post">
                <td>{$user.id}</td>
                <td><input type="text" name="username" value="{$user.username|escape}" class="form-control"></td>
                <td><input type="email" name="email" value="{$user.email|escape}" class="form-control"></td>
                <td><input type="text" name="nickname" value="{$user.nickname|escape}" class="form-control"></td>
                <td>
                    <select name="privilege" class="form-control">
                        <option value="user" {if $user.privilege == 'user'}selected{/if}>User</option>
                        <option value="admin" {if $user.privilege == 'admin'}selected{/if}>Admin</option>
                    </select>
                </td>
                <td>
                    <input type="hidden" name="id" value="{$user.id}">
                    <input type="password" name="password" class="form-control mb-1" placeholder="New password (optional)">
                    <button type="submit" name="edit_user" class="btn btn-primary btn-sm">Save</button>
                    <button type="submit" name="delete_user" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                </td>
                </form>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
