{* Trailer Management Template *}
<h2>Equipment</h2>
{if $is_admin}
<h3>
    <button class="btn btn-link text-decoration-none" type="button" id="toggleAddEditBtn">
        <span class="me-2 arrow" id="addEditArrow">➤</span> <span class="toggle-text" id="addEditText">Add Trailer</span>
    </button>
</h3>
<div id="addEditTrailerForm" style="display:none;">
    <form method="post" id="addEditForm">
        <input type="hidden" name="mode" id="addEditMode" value="add">
        <input type="hidden" name="id" id="addEditId" value="">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white fw-bold">Trailer Details</div>
            <div class="card-body">
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold" for="addEditTrlId">Trailer ID</label>
                        <input type="number" name="trl_id" id="addEditTrlId" class="form-control" required>
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
<h3>Active Trailers</h3>
<table class="table">
    <thead>
        <tr>
            <th>Trailer ID</th>
            {foreach $questions_first3 as $q}
                <th>{$q.label|escape}</th>
            {/foreach}
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    {foreach $active as $trl}
        {assign var=answers value=$trl.answers}
        <tr>
            <td>{$trl.trl_id|escape}</td>
            {foreach $trl.answers_first3 as $a}
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
                <a href="maintenance.php?trl_id={$trl.id}&type=trailer" class="btn btn-info btn-sm">View Maintenance</a>
                {if $is_admin}
                <a href="trailer.php?archive={$trl.id}" class="btn btn-warning btn-sm">Archive</a>
                <button class="btn btn-secondary btn-sm" onclick="editTrl({$trl.id}, '{$trl.answers_json}')">Edit</button>
                {/if}
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
{if $is_admin}
<h3>Archived Trailers</h3>
<table class="table">
    <thead>
        <tr>
            <th>Trailer ID</th>
            {foreach $questions_first3 as $q}
                <th>{$q.label|escape}</th>
            {/foreach}
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    {foreach $archived as $trl}
        {assign var=answers value=$trl.answers}
        <tr>
            <td>{$trl.trl_id|escape}</td>
            {foreach $trl.answers_first3 as $a}
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
                <a href="trailer.php?unarchive={$trl.id}" class="btn btn-success btn-sm">Unarchive</a>
                <a href="maintenance.php?trl_id={$trl.id}" class="btn btn-info btn-sm">View Maintenance</a>
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
    const formDiv = document.getElementById('addEditTrailerForm');
    const arrow = document.getElementById('addEditArrow');
    if (formDiv.style.display === 'none') {
        formDiv.style.display = 'block';
        arrow.textContent = '▼';
        document.getElementById('addEditText').textContent = 'Add Trailer';
    } else {
        formDiv.style.display = 'none';
        arrow.textContent = '➤';
    }
});

document.getElementById('addEditCancelBtn').addEventListener('click', function() {
    document.getElementById('addEditTrailerForm').style.display = 'none';
    document.getElementById('addEditArrow').textContent = '➤';
    document.getElementById('addEditMode').value = 'add';
    document.getElementById('addEditId').value = '';
    document.getElementById('addEditText').textContent = 'Add Trailer';
    document.getElementById('addEditTrlId').value = '';
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

// Map trailer id to trl_id for edit form population
var trailerIdToTrlId = {};
{foreach $active as $trl}
    trailerIdToTrlId[{$trl.id}] = '{$trl.trl_id|escape}';
{/foreach}

function editTrl(id, answersMapStr) {
    var answersMap = {};
    try {
        answersMap = JSON.parse(answersMapStr);
    } catch (e) {}
    const formDiv = document.getElementById('addEditTrailerForm');
    const arrow = document.getElementById('addEditArrow');
    document.getElementById('addEditMode').value = 'edit';
    document.getElementById('addEditId').value = id;
    document.getElementById('addEditText').textContent = 'Edit Trailer';
    document.getElementById('addEditTrlId').value = trailerIdToTrlId[id] || '';
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
