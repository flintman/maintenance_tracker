<h2>{$primary_label|escape} Units</h2>
{if $is_admin}
<h3>
    <button class="btn btn-link text-decoration-none" type="button" id="toggleAddEditBtn">
        <span class="me-2 arrow" id="addEditArrow">➤</span> <span class="toggle-text" id="addEditText">Add {$primary_label|escape}</span>
    </button>
</h3>
<div id="addEditPrimaryForm" style="display:none;">
    <form method="post" id="addEditForm">
        <input type="hidden" name="mode" id="addEditMode" value="add">
        <input type="hidden" name="id" id="addEditId" value="">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white fw-bold">{$primary_label|escape} Details</div>
            <div class="card-body">
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold" for="addEditPmyId">{$primary_label|escape} ID</label>
                        <input type="number" name="pmy_id" id="addEditPmyId" class="form-control" required>
                    </div>
                </div>
                <div class="row g-3">
                    {foreach $questions as $q}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold" for="addEditQ{$q.id}">{$q.label|escape}</label>
                        {if $q.type == 'multi_choice'}
                            <select name="question_{$q.id}[]" id="addEditQ{$q.id}" class="form-control" multiple>
                                {foreach $q.options|split:',' as $opt}
                                    <option value="{$opt|escape}">{$opt|escape}</option>
                                {/foreach}
                            </select>
                        {elseif $q.type == 'text'}
                            <textarea name="question_{$q.id}" id="addEditQ{$q.id}" class="form-control" rows="2"></textarea>
                        {elseif $q.type == 'number'}
                            <input type="number" name="question_{$q.id}" id="addEditQ{$q.id}" class="form-control">
                        {elseif $q.type == 'date'}
                            <input type="date" name="question_{$q.id}" id="addEditQ{$q.id}" class="form-control">
                        {else}
                            <input type="text" name="question_{$q.id}" id="addEditQ{$q.id}" class="form-control">
                        {/if}
                    </div>
                    {/foreach}
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-success px-4" id="addEditSubmitBtn">Submit</button>
            <button type="button" class="btn btn-secondary ms-2" id="addEditCancelBtn">Cancel</button>
        </div>
    </form>
</div>
{/if}
<h3>Active {$primary_label|escape} Units</h3>
<table class="table tablesorter modern-table">
    <thead>
        <tr>
            <th>{$primary_label|escape} ID</th>
            {foreach $questions_first as $q}
                <th>{$q.label|escape}</th>
            {/foreach}
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    {foreach $active as $pmy}
        {assign var=answers value=$pmy.answers}
        <tr>
            <td>{$pmy.pmy_id|escape}</td>
            {foreach $pmy.answers_first as $a}
                <td>
                    {if $a.type == 'multi_choice'}
                        {foreach $a.value|split:',' as $val}
                            <span class="badge bg-info text-dark">{$val|escape}</span>
                        {/foreach}
                    {else}
                        {$a.value|escape}
                    {/if}
                </td>
            {/foreach}
            <td>
                <a href="maintenance.php?pmy_id={$pmy.id}&type=primary" class="btn btn-info btn-sm">View Maintenance</a>
                {if $is_admin}
                <a href="primary.php?archive={$pmy.id}" class="btn btn-warning btn-sm">Archive</a>
                <button class="btn btn-secondary btn-sm" onclick="editPmy({$pmy.id}, '{$pmy.answers_json}')">Edit</button>
                {/if}
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
{if $is_admin}
<h3>Archived {$primary_label|escape} Units</h3>
<table class="table tablesorter modern-table">
    <thead>
        <tr>
            <th>{$primary_label|escape} ID</th>
            {foreach $questions_first as $q}
                <th>{$q.label|escape}</th>
            {/foreach}
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    {foreach $archived as $pmy}
        {assign var=answers value=$pmy.answers}
        <tr>
            <td>{$pmy.pmy_id|escape}</td>
            {foreach $pmy.answers_first as $a}
                <td>
                    {if $a.type == 'multi_choice'}
                        {foreach $a.value|split:',' as $val}
                            <span class="badge bg-info text-dark">{$val|escape}</span>
                        {/foreach}
                    {else}
                        {$a.value|escape}
                    {/if}
                </td>
            {/foreach}
            <td>
                <a href="primary.php?unarchive={$pmy.id}" class="btn btn-success btn-sm">Unarchive</a>
                <a href="maintenance.php?pmy_id={$pmy.id}" class="btn btn-info btn-sm">View Maintenance</a>
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
<script>
var questionInfo = [
{foreach $questions as $q}
    {ldelim}id: {$q.id}, type: '{$q.type}', label: '{$q.label|escape}'{rdelim},
{/foreach}
];

// Toggle add/edit form
document.getElementById('toggleAddEditBtn').addEventListener('click', function() {
    const formDiv = document.getElementById('addEditPrimaryForm');
    const arrow = document.getElementById('addEditArrow');
    if (formDiv.style.display === 'none') {
        formDiv.style.display = 'block';
        arrow.textContent = '▼';
        document.getElementById('addEditText').textContent = 'Add {$primary_label|escape}';
    } else {
        formDiv.style.display = 'none';
        arrow.textContent = '➤';
    }
});

document.getElementById('addEditCancelBtn').addEventListener('click', function() {
    document.getElementById('addEditPrimaryForm').style.display = 'none';
    document.getElementById('addEditArrow').textContent = '➤';
    document.getElementById('addEditMode').value = 'add';
    document.getElementById('addEditId').value = '';
    document.getElementById('addEditText').textContent = 'Add {$primary_label|escape}';
    document.getElementById('addEditPmyId').value = '';
    // Clear dynamic question fields
    questionInfo.forEach(function(q) {
        var el = document.getElementById('addEditQ' + q.id);
        if (el) {
            if (q.type === 'multi_choice') {
                for (var i = 0; i < el.options.length; i++) {
                    el.options[i].selected = false;
                }
            } else {
                el.value = '';
            }
        }
    });
});

// Map primary id to pmy_id for edit form population
var primaryIdToPmyId = {};
{foreach $active as $pmy}
    primaryIdToPmyId[{$pmy.id}] = '{$pmy.pmy_id|escape}';
{/foreach}

function editPmy(id, answersMapStr) {
    var answersMap = {};
    try {
        answersMap = JSON.parse(answersMapStr);
    } catch (e) {}
    const formDiv = document.getElementById('addEditPrimaryForm');
    const arrow = document.getElementById('addEditArrow');
    document.getElementById('addEditMode').value = 'edit';
    document.getElementById('addEditId').value = id;
    document.getElementById('addEditText').textContent = 'Edit {$primary_label|escape}';
    document.getElementById('addEditPmyId').value = primaryIdToPmyId[id] || '';
    // Populate dynamic question fields using answersMap
    questionInfo.forEach(function(q) {
        var val = answersMap[q.label] || '';
        var el = document.getElementById('addEditQ' + q.id);
        if (el) {
            if (q.type === 'multi_choice') {
                var opts = val.split(',');
                for (var i = 0; i < el.options.length; i++) {
                    el.options[i].selected = opts.includes(el.options[i].value);
                }
            } else {
                el.value = val;
            }
        }
    });
    formDiv.style.display = 'block';
    arrow.textContent = '▼';
    window.scrollTo(0,document.body.scrollHeight);
}
</script>
{/if}
