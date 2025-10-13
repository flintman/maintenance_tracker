<div class="container mt-5">
    <h2>Manage {$secondary_label|escape} Questions</h2>
    <form method="post" class="mb-4">
        <div class="row">
            <div class="col-md-2">
                <input type="text" name="label" class="form-control" placeholder="Question label" required>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-control" id="typeSelect" required onchange="toggleOptionsInput(this)">
                    <option value="string">Short Text</option>
                    <option value="text">Long Text (textarea)</option>
                    <option value="number">Number</option>
                    <option value="date">Date</option>
                    <option value="multi_choice">Multi-Choice</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="options" id="optionsInput" class="form-control" style="display:none" placeholder="Choices (comma separated)">
            </div>
            <div class="col-md-2">
                <input type="number" name="position" class="form-control" placeholder="Order" min="0" value="{count($questions)}">
            </div>
            <div class="col-md-2">
                <button type="submit" name="add_question" class="btn btn-success">Add Question</button>
            </div>
        </div>
    </form>
    <table class="table table-bordered">
        <thead><tr><th>Label</th><th>Type</th><th>Options</th><th>Order</th><th>Actions</th></tr></thead>
        <tbody>
            {foreach $questions as $q}
            <tr>
                <form method="post">
                    <td><input type="text" name="label" value="{$q.label|escape}" class="form-control"></td>
                    <td>
                        <select name="type" class="form-control" onchange="toggleOptionsInput(this)">
                            <option value="string" {if $q.type=='string'}selected{/if}>Short Text</option>
                            <option value="text" {if $q.type=='text'}selected{/if}>Long Text</option>
                            <option value="number" {if $q.type=='number'}selected{/if}>Number</option>
                            <option value="date" {if $q.type=='date'}selected{/if}>Date</option>
                            <option value="multi_choice" {if $q.type=='multi_choice'}selected{/if}>Multi-Choice</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="options" value="{$q.options|escape}" class="form-control" {if $q.type!='multi_choice'}style="display:none"{/if} placeholder="Choices (comma separated)">
                    </td>
                    <td>
                        <input type="number" name="position" value="{$q.position}" class="form-control" min="0">
                        <button type="submit" name="move_up" class="btn btn-sm btn-secondary">↑</button>
                        <button type="submit" name="move_down" class="btn btn-sm btn-secondary">↓</button>
                    </td>
                    <td>
                        <input type="hidden" name="id" value="{$q.id}">
                        <button type="submit" name="edit_question" class="btn btn-primary btn-sm">Save</button>
                        <button type="submit" name="delete_question" class="btn btn-danger btn-sm" onclick="return confirm('Delete this question?')">Delete</button>
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
