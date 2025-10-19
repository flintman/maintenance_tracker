<div class="container mt-5">
    <h2><i class="fas fa-bullhorn me-2"></i>{$ADMIN_MANAGE_MESSAGE_TITLE}</h2>

    {if $success_msg}
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{$success_msg}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    {/if}

    {if $error_msg}
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{$error_msg}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    {/if}

    <!-- Add New Message Form -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-plus me-2"></i>{$ADMIN_ADD_MESSAGE_TITLE}</h5>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label for="new_message" class="form-label">{$ADMIN_MESSAGE_CONTENT_LABEL}</label>
                    <textarea class="form-control" id="new_message" name="message" rows="4" required
                              placeholder="{$ADMIN_MESSAGE_PLACEHOLDER}"></textarea>
                </div>
                <button type="submit" name="add_message" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>{$ADMIN_ADD_MESSAGE_BUTTON}
                </button>
            </form>
        </div>
    </div>

    <!-- Messages List -->
    <div class="card">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>{$ADMIN_ALL_MESSAGES_TITLE}</h5>
            <small>{$ADMIN_ACTIVE_FIRST_NOTE}</small>
        </div>
        <div class="card-body">
            {if $messages}
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">{$ADMIN_STATUS}</th>
                                <th>{$ADMIN_MESSAGE}</th>
                                <th width="150">{$ADMIN_DATE_CREATED}</th>
                                <th width="200">{$ADMIN_ACTIONS}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $messages as $msg}
                            <tr class="{if $msg.active}table-warning{/if}">
                                <td>
                                    {if $msg.active}
                                            <span class="badge bg-success">
                                                <i class="fas fa-eye me-1"></i>{$ADMIN_ACTIVE}
                                            </span>
                                        {else}
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-eye-slash me-1"></i>{$ADMIN_INACTIVE}
                                            </span>
                                        {/if}
                                </td>
                                <td>
                                    <div id="view_msg_{$msg.id}">{$msg.message|escape|nl2br}</div>
                                    <div id="edit_msg_{$msg.id}" style="display: none;">
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="message_id" value="{$msg.id}">
                                            <textarea class="form-control mb-2" name="message" rows="3" required>{$msg.message|escape}</textarea>
                                            <button type="submit" name="edit_message" class="btn btn-success btn-sm">
                                                <i class="fas fa-save me-1"></i>{$SAVE_TITLE|escape}
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleEdit({$msg.id})">
                                                <i class="fas fa-times me-1"></i>{$CANCEL_BUTTON|escape}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {$msg.performed_at|date_format:"%Y-%m-%d %H:%M"}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group-vertical btn-group-sm" role="group">
                                        {if !$msg.active}
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="message_id" value="{$msg.id}">
                                                    <button type="submit" name="activate_message" class="btn btn-success btn-sm"
                                                            onclick="return confirm('{$ADMIN_ACTIVATE_CONFIRM}')">
                                                        <i class="fas fa-eye me-1"></i>{$ADMIN_ACTIVATE_BUTTON}
                                                    </button>
                                            </form>
                                        {/if}

                                        <button type="button" class="btn btn-warning btn-sm" onclick="toggleEdit({$msg.id})">
                                            <i class="fas fa-edit me-1"></i>{$ADMIN_EDIT_BUTTON}
                                        </button>

                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="message_id" value="{$msg.id}">
                                            <button type="submit" name="delete_message" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('{$ADMIN_DELETE_CONFIRM}')">
                                                <i class="fas fa-trash me-1"></i>{$ADMIN_DELETE_BUTTON}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            {else}
                <div class="text-center text-muted py-5">
                    <i class="fas fa-comment-slash fa-3x mb-3"></i>
                    <h5>{$ADMIN_NO_MESSAGES_TITLE}</h5>
                    <p>{$ADMIN_NO_MESSAGES_DESC}</p>
                </div>
            {/if}
        </div>
    </div>
</div>

<script>
function toggleEdit(messageId) {
    const viewDiv = document.getElementById('view_msg_' + messageId);
    const editDiv = document.getElementById('edit_msg_' + messageId);

    if (viewDiv.style.display === 'none') {
        viewDiv.style.display = 'block';
        editDiv.style.display = 'none';
    } else {
        viewDiv.style.display = 'none';
        editDiv.style.display = 'block';
    }
}
</script>