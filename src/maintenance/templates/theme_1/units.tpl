<h2>{$unit_label|escape} {$UNITS_TITLE|escape}</h2>
{if $is_admin}
    <h3>
    <button class="btn btn-link text-decoration-none" type="button" id="toggleAddEditBtn">
        <span class="me-2 arrow" id="addEditArrow">➤</span> <span class="toggle-text" id="addEditText">{$BTN_ADD|escape} {$unit_label|escape}</span>
    </button>
</h3>
<div id="addEditPrimaryForm" style="display:none;">
    <form method="post" id="addEditForm">
        <input type="hidden" name="mode" id="addEditMode" value="add">
        <input type="hidden" name="id" id="addEditId" value="">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white fw-bold">{$unit_label|escape} {$DETAILS_TITLE|escape}</div>
            <div class="card-body">
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold" for="addEditUnitId">{$primary_label|escape} {$TH_ID|escape}</label>
                          {if !$secondary_id}
                                <input type="number" name="unit_id" id="addEditUnitId" class="form-control" required>
                            {else}
                                <select name="unit_id" id="addEditUnitId" class="form-control" required>
                                    <option value=""></option>
                                    {foreach $primary_units as $pmy}
                                        <option value="{$pmy.unit_id}">{$pmy.unit_id}</option>
                                    {/foreach}
                                </select>
                            {/if}
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
            <button class="btn btn-success px-4" id="addEditSubmitBtn">{$BTN_SUBMIT|escape}</button>
                <button type="button" class="btn btn-secondary ms-2" id="addEditCancelBtn">{$BTN_CANCEL|escape}</button>
        </div>
    </form>
</div>
{/if}
<h3>{$ACTIVE_LABEL|escape} {$unit_label|escape} {$UNITS_TITLE|escape}</h3>

<div id="activePrimaryTable" class="sortable-table">
    <div class="row mb-3">
        <div class="col-md-6">
            <input class="form-control search" placeholder="{$SEARCH_ACTIVE_PLACEHOLDER|escape}" />
        </div>
    </div>


    <table class="table modern-table">
        <thead>
            <tr>
                <th class="sort" data-sort="unit_id" style="cursor: pointer;">{$primary_label|escape} {$TH_ID|escape}</th>
                {foreach $questions_first as $q}
                    <th class="sort" data-sort="answer_{$q@index}" style="cursor: pointer;">{$q.label|escape}</th>
                {/foreach}
                    <th>{$TH_ACTIONS|escape}</th>
            </tr>
        </thead>
        <tbody class="list">
        {foreach $active as $pmy}
            {assign var=answers value=$pmy.answers}
            <tr>
                <td class="unit_id">{$pmy.unit_id|escape}</td>
                {foreach $pmy.answers_first as $a}
                    <td class="answer_{$a@index}">
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
                    <a href="maintenance.php?{if $number_unit == 'secondary'}&secondary_id={$pmy.id}{else}unit_id={$pmy.id}{/if}" class="btn btn-info btn-sm">{$VIEW_MAINTENANCE_BUTTON|escape}</a>
                    {if $is_admin}
                        <a href="units.php?archive={$pmy.id}{if $number_unit == 'secondary'}&secondary=1{/if}" class="btn btn-warning btn-sm">{$ARCHIVE_BUTTON|escape}</a>
                        <button class="btn btn-secondary btn-sm" onclick="editPmy({$pmy.id}, '{$pmy.answers_json}')">{$EDIT_BUTTON|escape}</button>
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    <div class="row mt-3">
        <div class="col-md-6">
            <ul class="pagination"></ul>
        </div>
        <div class="col-md-6 text-end">
            <span class="list-info text-muted"></span>
        </div>
    </div>

    {if $is_admin}
    <h3>{$ARCHIVED_LABEL|escape} {$unit_label|escape} {$UNITS_TITLE|escape}</h3>

    <div id="archivedPrimaryTable" class="sortable-table">
        <div class="row mb-3">
            <div class="col-md-6">
                <input class="form-control search" placeholder="{$SEARCH_ARCHIVED_PLACEHOLDER|escape}" />
            </div>
        </div>

        <table class="table modern-table">
            <thead>
                <tr>
                    <th class="sort" data-sort="unit_id" style="cursor: pointer;">{$primary_label|escape} {$TH_ID|escape}</th>
                    {foreach $questions_first as $q}
                        <th class="sort" data-sort="answer_{$q@index}" style="cursor: pointer;">{$q.label|escape}</th>
                    {/foreach}
                    <th>{$TH_ACTIONS|escape}</th>
                </tr>
            </thead>
            <tbody class="list">
            {foreach $archived as $pmy}
                {assign var=answers value=$pmy.answers}
                <tr>
                    <td class="unit_id">{$pmy.unit_id|escape}</td>
                    {foreach $pmy.answers_first as $a}
                        <td class="answer_{$a@index}">
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
                        <a href="units.php?unarchive={$pmy.id}{if $number_unit == 'secondary'}&secondary=1{/if}" class="btn btn-success btn-sm">{$UNARCHIVE_BUTTON|escape}</a>
                        <a href="maintenance.php?{if $number_unit == 'secondary'}&secondary_id={$pmy.id}{else}pmy_id={$pmy.id}{/if}" class="btn btn-info btn-sm">{$VIEW_MAINTENANCE_BUTTON|escape}</a>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        <div class="row mt-3">
            <div class="col-md-6">
                <ul class="pagination"></ul>
            </div>
            <div class="col-md-6 text-end">
                <span class="list-info text-muted"></span>
            </div>
        </div>
    </div>
</div>
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
    if (formDiv.style.display === 'none' || formDiv.style.display === '') {
        formDiv.style.display = 'block';
        arrow.textContent = '▼';
        document.getElementById('addEditText').textContent = '{$BTN_ADD|escape} {$unit_label|escape}';
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
    document.getElementById('addEditText').textContent = '{$BTN_ADD|escape} {$unit_label|escape}';
    var unitIdInput = document.getElementById('addEditUnitId');
    if (unitIdInput) {
        unitIdInput.value = '';
    }
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

// Map primary id to unit_id for edit form population
var primaryIdToUnitId = {};
{foreach $active as $pmy}
    primaryIdToUnitId[{$pmy.id}] = '{$pmy.unit_id|escape}';
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
    document.getElementById('addEditText').textContent = '{$ADMIN_EDIT_BUTTON|escape} {$unit_label|escape}';
    var unitIdInput = document.getElementById('addEditUnitId');
    if (unitIdInput) {
        unitIdInput.value = primaryIdToUnitId[id] || '';
    }
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
    window.scrollTo(0, 0);
}

// Initialize List.js tables
document.addEventListener('DOMContentLoaded', function() {
    // Active Primary Units Table
    if (document.getElementById('activePrimaryTable')) {
        var valueNames = ['unit_id'];
        {foreach $questions_first as $q}
            valueNames.push('answer_{$q@index}');
        {/foreach}

        var activePrimaryList = new List('activePrimaryTable', {
            valueNames: valueNames,
            pagination: true,
            page: 10,
            searchClass: 'search',
            listClass: 'list'
        });
    }

    // Archived Primary Units Table (only if admin)
    {if $is_admin}
    if (document.getElementById('archivedPrimaryTable')) {
        var archivedValueNames = ['unit_id'];
        {foreach $questions_first as $q}
            archivedValueNames.push('answer_{$q@index}');
        {/foreach}

        var archivedPrimaryList = new List('archivedPrimaryTable', {
            valueNames: archivedValueNames,
            pagination: true,
            page: 10,
            searchClass: 'search',
            listClass: 'list'
        });
    }
    {/if}
});
</script>
{/if}