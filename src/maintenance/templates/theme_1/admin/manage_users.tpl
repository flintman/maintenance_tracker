<div class="container mt-5">
    <h2>{$ADMIN_MANAGE_USERS_TITLE|escape}</h2>
    {$edit_msg nofilter}{$delete_msg nofilter}
    <h3 class="mt-4">{$ADMIN_ALL_USERS_TITLE|escape}</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>{$TH_ID|escape}</th>
                <th>{$USERNAME_TITLE|escape}</th>
                <th>{$EMAIL_TITLE|escape}</th>
                <th>{$NICKNAME_LABEL|escape}</th>
                <th>{$PRIVILEGE_TITLE|escape}</th>
                <th>{$TH_ACTIONS|escape}</th>
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
                        <option value="user" {if $user.privilege == 'user'}selected{/if}>{$PRIVILEGE_USER|escape}</option>
                        <option value="admin" {if $user.privilege == 'admin'}selected{/if}>{$PRIVILEGE_ADMIN|escape}</option>
                    </select>
                </td>
                <td>
                    <input type="hidden" name="id" value="{$user.id}">
                    <input type="password" name="password" class="form-control mb-1" placeholder="{$PASSWORD_PLACEHOLDER_NEW|escape}">
                    <button type="submit" name="edit_user" class="btn btn-primary btn-sm">{$BTN_SAVE|escape}</button>
                    <button type="submit" name="delete_user" class="btn btn-danger btn-sm" onclick="return confirm('{$ADMIN_DELETE_CONFIRM_USER|escape}')">{$BTN_DELETE|escape}</button>
                </td>
                </form>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
