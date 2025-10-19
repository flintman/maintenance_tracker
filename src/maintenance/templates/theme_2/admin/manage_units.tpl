<div class="container mt-5">
    <h2>{$ADMIN_MANAGE_LABEL|escape} {$unit_label|escape} {$QUESTIONS_TITLE|escape}</h2>
    <form method="post" class="mb-4">
        <div class="row">
            <div class="col-md-2">
                <input type="text" name="label" class="form-control" placeholder="{$QUESTION_LABEL_PLACEHOLDER|escape}" required>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-control" id="typeSelect" required onchange="toggleOptionsInput(this)">
                    <option value="string">{$TYPE_SHORT_TEXT|escape}</option>
                    <option value="text">{$TYPE_LONG_TEXT|escape}</option>
                    <option value="number">{$TYPE_NUMBER|escape}</option>
                    <option value="date">{$TYPE_DATE|escape}</option>
                    <option value="multi_choice">{$TYPE_MULTI_CHOICE|escape}</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="options" id="optionsInput" class="form-control" style="display:none" placeholder="{$CHOICES_PLACEHOLDER|escape}">
            </div>
            <div class="col-md-2">
                <input type="number" name="position" class="form-control" placeholder="Order" min="0" value="{count($questions)}">
            </div>
            <div class="col-md-2">
                <button type="submit" name="add_question" class="btn btn-success">{$BTN_ADD_QUESTION|escape}</button>
            </div>
        </div>
    </form>
    <table class="table table-bordered">
    <thead><tr><th>{$TH_LABEL|escape}</th><th>{$TH_TYPE|escape}</th><th>{$TH_OPTIONS|escape}</th><th>{$TH_ORDER|escape}</th><th>{$TH_ACTIONS|escape}</th></tr></thead>
        <tbody>
            {foreach $questions as $q}
            <tr>
                <form method="post">
                    <td><input type="text" name="label" value="{$q.label|escape}" class="form-control"></td>
                    <td>
                        <select name="type" class="form-control" onchange="toggleOptionsInput(this)">
                            <option value="string" {if $q.type=='string'}selected{/if}>{$TYPE_SHORT_TEXT|escape}</option>
                            <option value="text" {if $q.type=='text'}selected{/if}>{$TYPE_LONG_TEXT|escape}</option>
                            <option value="number" {if $q.type=='number'}selected{/if}>{$TYPE_NUMBER|escape}</option>
                            <option value="date" {if $q.type=='date'}selected{/if}>{$TYPE_DATE|escape}</option>
                            <option value="multi_choice" {if $q.type=='multi_choice'}selected{/if}>{$TYPE_MULTI_CHOICE|escape}</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="options" value="{$q.options|escape}" class="form-control" {if $q.type!='multi_choice'}style="display:none"{/if} placeholder="{$CHOICES_PLACEHOLDER|escape}">
                    </td>
                    <td>
                        <input type="number" name="position" value="{$q.position}" class="form-control" min="0">
                        <button type="submit" name="move_up" class="btn btn-sm btn-secondary">{$ACTION_MOVE_UP|escape}</button>
                        <button type="submit" name="move_down" class="btn btn-sm btn-secondary">{$ACTION_MOVE_DOWN|escape}</button>
                    </td>
                    <td>
                        <input type="hidden" name="id" value="{$q.id}">
                        <button type="submit" name="edit_question" class="btn btn-primary btn-sm">{$BTN_SAVE|escape}</button>
                        <button type="submit" name="delete_question" class="btn btn-danger btn-sm" onclick="return confirm('{$CONFIRM_DELETE_QUESTION|escape}')">{$BTN_DELETE|escape}</button>
                    </td>
                </form>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
<script>
function toggleOptionsInput(sel) {
    var optionsInput = sel.parentNode.parentNode.querySelector('input[name="options"]');
    if (sel.value === 'multi_choice') {
        optionsInput.style.display = 'block';
    } else {
        optionsInput.style.display = 'none';
    }
}
</script>
