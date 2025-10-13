<script>
let lastEditId = null;
let addEditMode = 'add';
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('toggleAddEditBtn').addEventListener('click', function() {
        const formDiv = document.getElementById('addEditSecondaryForm');
        const arrow = document.getElementById('addEditArrow');
        if (formDiv.style.display === 'none') {
            formDiv.style.display = 'block';
            arrow.textContent = '▼';
            document.getElementById('addEditText').textContent = addEditMode === 'edit' ? 'Edit {$secondary_label|escape} Unit' : 'Add {$secondary_label|escape} Unit';
        } else {
            formDiv.style.display = 'none';
            arrow.textContent = '➤';
        }
    });

    document.getElementById('addEditCancelBtn').addEventListener('click', function() {
        document.getElementById('addEditSecondaryForm').style.display = 'none';
        document.getElementById('addEditArrow').textContent = '➤';
        addEditMode = 'add';
        document.getElementById('addEditMode').value = 'add';
        document.getElementById('addEditId').value = '';
        document.getElementById('addEditText').textContent = 'Add {$secondary_label|escape} Unit';
        document.getElementById('addEditPmyId').value = '';
        // Clear all question fields
        {foreach $questions as $q}
        var el = document.getElementById('addEditQ{$q.id}');
        if (el) {
            if ('{$q.type}' === 'multi_choice') {
                for (var i = 0; i < el.options.length; i++) {
                    el.options[i].selected = false;
                }
            } else {
                el.value = '';
            }
        }
        {/foreach}
    });
});

function editUnit(id, pmyId) {
    const formDiv = document.getElementById('addEditSecondaryForm');
    const arrow = document.getElementById('addEditArrow');
    addEditMode = 'edit';
    document.getElementById('addEditMode').value = 'edit';
    document.getElementById('addEditId').value = id;
    document.getElementById('addEditText').textContent = 'Edit {$secondary_label|escape} Unit';
    document.getElementById('addEditPmyId').value = pmyId;
    // Build answers map
    var answersMap = {};
    {foreach $units as $unit}
        answersMap[{$unit.id}] = {};
        {foreach $unit.answers as $a}
            answersMap[{$unit.id}]['{$a.label|escape}'] = '{$a.value|escape}';
        {/foreach}
    {/foreach}
    // Populate question fields
    {foreach $questions as $q}
    var el = document.getElementById('addEditQ{$q.id}');
    var val = answersMap[id] ? answersMap[id]['{$q.label|escape}'] : '';
    if (el) {
        if ('{$q.type}' === 'multi_choice') {
            var opts = val ? val.split(',') : [];
            for (var i = 0; i < el.options.length; i++) {
                el.options[i].selected = opts.includes(el.options[i].value);
            }
        } else {
            el.value = val;
        }
    }
    {/foreach}
    formDiv.style.display = 'block';
    arrow.textContent = '▼';
    window.scrollTo(0,document.body.scrollHeight);
}
</script>
<h2>{$secondary_label|escape} Units</h2>
{if $session.privilege == 'admin'}
<h3>
    <button class="btn btn-link text-decoration-none" type="button" id="toggleAddEditBtn">
        <span class="me-2 arrow" id="addEditArrow">➤</span> <span class="toggle-text" id="addEditText">Add {$secondary_label|escape} Unit</span>
    </button>
</h3>
{/if}

<div id="addEditSecondaryForm" style="display:none;">
    <form method="post" id="addEditForm">
        <input type="hidden" name="mode" id="addEditMode" value="add">
        <input type="hidden" name="id" id="addEditId" value="">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white fw-bold">{$secondary_label|escape} Unit Details</div>
            <div class="card-body">
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold" for="addEditPmyId">{$primary_label|escape} ID</label>
                        <select name="pmy_id" id="addEditPmyId" class="form-control" required>
                            <option value=""></option>
                            {foreach $primary_units as $pmy}
                                <option value="{$pmy.pmy_id}">{$pmy.pmy_id}</option>
                            {/foreach}
                        </select>
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

<h3>Active {$secondary_label|escape} Units</h3>
<div class="table-responsive">
    <input type="text" class="tableSearch" data-table="activeTable" placeholder="Search..." style="margin-bottom:10px;">
    <table class="table tablesorter modern-table" id="activeTable">
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
            {foreach $units as $unit}
                {assign var=answers value=$unit.answers}
                <tr>
                    <td>{$unit.pmy_id|escape}</td>
                    {foreach $unit.answers_first as $a}
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
                        <a href="maintenance.php?secondary_id={$unit.id}&type=secondary_units" class="btn btn-info btn-sm">View Maintenance</a>
                        {if $session.privilege == 'admin'}
                        <form method="post" style="display:inline-block">
                            <input type="hidden" name="id" value="{$unit.id}">
                            <button name="archive" class="btn btn-warning btn-sm">Archive</button>
                        </form>
                        <button class="btn btn-secondary btn-sm" onclick="editUnit({$unit.id}, {$unit.pmy_id})">Edit</button>
                        {/if}
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
</div>

{if $session.privilege == 'admin'}
<h3>Archived {$secondary_label|escape} Units</h3>
<div class="table-responsive">
    <input type="text" class="tableSearch" data-table="archivedTable" placeholder="Search..." style="margin-bottom:10px;">
    <table class="table tablesorter modern-table" id="archivedTable">
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
            {foreach $archived as $unit}
                {assign var=answers value=$unit.answers}
                <tr>
                    <td>{$unit.pmy_id|escape}</td>
                    {foreach $unit.answers_first as $a}
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
                        <form method="post" style="display:inline-block">
                            <input type="hidden" name="id" value="{$unit.id}">
                            <button name="unarchive" class="btn btn-success btn-sm">Unarchive</button>
                        </form>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
</div>
{/if}
