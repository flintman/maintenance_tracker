<style>
    [data-bs-theme="dark"] .modern-table thead,
    [data-bs-theme="dark"] .modern-table .table-light,
    [data-bs-theme="dark"] .modern-table th {
        background-color: #232526 !important;
        color: #e0e0e0 !important;
        border-color: #444 !important;
    }
    .modern-btn-info {
        background: linear-gradient(90deg, #232526 0%, #00bcd4 100%) !important;
        color: #fff !important;
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    }
    .modern-btn-warning {
        background: linear-gradient(90deg, #232526 0%, #ff9800 100%) !important;
        color: #fff !important;
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    }
    .modern-btn-secondary {
        background: linear-gradient(90deg, #232526 0%, #607d8b 100%) !important;
        color: #fff !important;
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    }
    .modern-btn-success {
        background: linear-gradient(90deg, #232526 0%, #4caf50 100%) !important;
        color: #fff !important;
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    }
</style>
<script>
var questionInfo = [
{foreach $questions as $q}
    {ldelim}id: {$q.id}, type: '{$q.type}', label: '{$q.label|escape}'{rdelim},
{/foreach}
];
// Toggle add/edit form
document.addEventListener('DOMContentLoaded', function() {
    var toggleBtn = document.getElementById('toggleAddEditBtn');
            if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const formDiv = document.getElementById('addEditPrimaryForm');
            const arrow = document.getElementById('addEditArrow');
            if (formDiv.style.display === 'none') {
                formDiv.style.display = 'block';
                arrow.textContent = '▼';
                document.getElementById('addEditText').textContent = '{$BTN_ADD|escape} {$unit_label|escape}';
            } else {
                formDiv.style.display = 'none';
                arrow.textContent = '➤';
            }
        });
    }
    var cancelBtn = document.getElementById('addEditCancelBtn');
            if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            document.getElementById('addEditPrimaryForm').style.display = 'none';
            document.getElementById('addEditArrow').textContent = '➤';
            document.getElementById('addEditMode').value = 'add';
            document.getElementById('addEditId').value = '';
            document.getElementById('addEditText').textContent = '{$BTN_ADD|escape} {$unit_label|escape}';
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
    }
});
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
    document.getElementById('addEditText').textContent = '{$EDIT_BUTTON|escape} {$unit_label|escape}';
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
<div class="container py-4">
    <h1 class="display-5 fw-bold mb-4">{$unit_label|escape} {$UNITS_TITLE|escape}</h1>
    {if isset($msg)}<div class="alert alert-info">{$msg}</div>{/if}
    {if $is_admin}
    <div class="mb-4">
        <button class="btn modern-btn modern-btn-primary" type="button" id="toggleAddEditBtn">
            <span class="me-2 arrow" id="addEditArrow">➤</span> <span class="toggle-text" id="addEditText">{$BTN_ADD|escape} {$unit_label|escape}</span>
        </button>
    </div>
    <div id="addEditPrimaryForm" style="display:none;">
        <form method="post" id="addEditForm">
            <input type="hidden" name="mode" id="addEditMode" value="add">
            <input type="hidden" name="id" id="addEditId" value="">
            <div class="card modern-card shadow-sm mb-4">
                <div class="card-header bg-gradient text-white fw-bold" style="background: linear-gradient(90deg, #232526 0%, #00bcd4 100%) !important;">{$unit_label|escape} {$DETAILS_TITLE|escape}</div>
                <div class="card-body">
                    <div class="row g-3 align-items-center mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="addEditPmyId">{$primary_label|escape} {$DETAILS_TITLE|escape} {$TH_ID|escape}</label>
                            {if !$secondary_id}
                                <input type="number" name="pmy_id" id="addEditPmyId" class="form-control" required>
                            {else}
                                <select name="pmy_id" id="addEditPmyId" class="form-control" required>
                                    <option value=""></option>
                                    {foreach $primary_units as $pmy}
                                        <option value="{$pmy.pmy_id}">{$pmy.pmy_id}</option>
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
                <button class="btn modern-btn modern-btn-primary px-4" id="addEditSubmitBtn">{$BTN_SUBMIT|escape}</button>
                <button type="button" class="btn modern-btn modern-btn-secondary ms-2" id="addEditCancelBtn">{$BTN_CANCEL|escape}</button>
            </div>
        </form>
    </div>
    {/if}
    <h3 class="mt-5 mb-3">{$ACTIVE_LABEL|escape} {$unit_label|escape}s</h3>

    <div id="activePrimaryTable" class="sortable-table">
        <div class="row mb-3">
            <div class="col-md-6">
                <input class="form-control search" placeholder="{$SEARCH_ACTIVE_PLACEHOLDER|escape}" />
            </div>
        </div>

        <div class="table-responsive">
            <table class="table modern-table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="sort" data-sort="pmy_id" style="cursor: pointer;">{$unit_label|escape} {$DETAILS_TITLE|escape} {$TH_ID|escape}</th>
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
                            <td class="pmy_id fw-semibold">{$pmy.pmy_id|escape}</td>
                            {foreach $pmy.answers_first as $a}
                                <td class="answer_{$a@index}">
                                    {if $a.type == 'multi_choice'}
                                        {foreach $a.value|split:',' as $val}
                                            <span class="badge modern-badge bg-info text-dark">{$val|escape}</span>
                                        {/foreach}
                                    {else}
                                        <span class="text-body">{$a.value|escape}</span>
                                    {/if}
                                </td>
                            {/foreach}
                        <td>
                            <a href="maintenance.php?{if $number_unit == 'secondary'}&secondary_id={$pmy.id}{else}pmy_id={$pmy.id}{/if}" class="btn modern-btn modern-btn-info btn-sm">{$VIEW_MAINTENANCE_BUTTON|escape}</a>
                            {if $is_admin}
                            <a href="units.php?archive={$pmy.id}{if $number_unit == 'secondary'}&secondary=1{/if}" class="btn modern-btn modern-btn-warning btn-sm">{$ARCHIVE_BUTTON|escape}</a>
                            <button class="btn modern-btn modern-btn-secondary btn-sm" onclick="editPmy({$pmy.id}, '{$pmy.answers_json}')">{$EDIT_BUTTON|escape}</button>
                            {/if}
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <ul class="pagination"></ul>
            </div>
            <div class="col-md-6 text-end">
                <span class="list-info text-muted"></span>
            </div>
        </div>
    </div>

    {if $is_admin}
    <h3 class="mt-5 mb-3">{$ARCHIVED_LABEL|escape} {$unit_label|escape}s</h3>

    <div id="archivedPrimaryTable" class="sortable-table">
        <div class="row mb-3">
                            <div class="col-md-6">
                <input class="form-control search" placeholder="{$SEARCH_ARCHIVED_PLACEHOLDER|escape}" />
            </div>
        </div>

        <div class="table-responsive">
            <table class="table modern-table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="sort" data-sort="pmy_id" style="cursor: pointer;">{$unit_label|escape} {$DETAILS_TITLE|escape} {$TH_ID|escape}</th>
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
                            <td class="pmy_id fw-semibold">{$pmy.pmy_id|escape}</td>
                            {foreach $pmy.answers_first as $a}
                                <td class="answer_{$a@index}">
                                    {if $a.type == 'multi_choice'}
                                        {foreach $a.value|split:',' as $val}
                                            <span class="badge modern-badge bg-info text-dark">{$val|escape}</span>
                                        {/foreach}
                                    {else}
                                        <span class="text-body">{$a.value|escape}</span>
                                    {/if}
                                </td>
                            {/foreach}
                        <td>
                            <a href="units.php?unarchive={$pmy.id}{if $number_unit == 'secondary'}&secondary=1{/if}" class="btn modern-btn modern-btn-success btn-sm">{$UNARCHIVE_BUTTON|escape}</a>
                            <a href="maintenance.php?{if $number_unit == 'secondary'}&secondary_id={$pmy.id}{else}pmy_id={$pmy.id}{/if}" class="btn modern-btn modern-btn-info btn-sm">{$VIEW_MAINTENANCE_BUTTON|escape}</a>
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <ul class="pagination"></ul>
            </div>
            <div class="col-md-6 text-end">
                <span class="list-info text-muted"></span>
            </div>
        </div>
    </div>
    {/if}
</div>

<script>
// Initialize List.js tables
document.addEventListener('DOMContentLoaded', function() {
    // Active Primary Units Table
    if (document.getElementById('activePrimaryTable')) {
        var valueNames = ['pmy_id'];
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
        var archivedValueNames = ['pmy_id'];
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