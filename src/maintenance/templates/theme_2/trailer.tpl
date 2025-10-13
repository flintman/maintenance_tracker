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
    }
    var cancelBtn = document.getElementById('addEditCancelBtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
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
    }
});
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
    }
    var cancelBtn = document.getElementById('addEditCancelBtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
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
    }
});
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
<div class="container py-4">
    <h1 class="display-5 fw-bold mb-4">Equipment</h1>
    {if isset($msg)}<div class="alert alert-info">{$msg}</div>{/if}
    {if $is_admin}
    <div class="mb-4">
        <button class="btn modern-btn modern-btn-primary" type="button" id="toggleAddEditBtn">
            <span class="me-2 arrow" id="addEditArrow">➤</span> <span class="toggle-text" id="addEditText">Add Trailer</span>
        </button>
    </div>
    <div id="addEditTrailerForm" style="display:none;">
        <form method="post" id="addEditForm">
            <input type="hidden" name="mode" id="addEditMode" value="add">
            <input type="hidden" name="id" id="addEditId" value="">
            <div class="card modern-card shadow-sm mb-4">
                <div class="card-header bg-gradient text-white fw-bold" style="background: linear-gradient(90deg, #232526 0%, #00bcd4 100%) !important;">Trailer Details</div>
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
                <button class="btn modern-btn modern-btn-primary px-4" id="addEditSubmitBtn">Submit</button>
                <button type="button" class="btn modern-btn modern-btn-secondary ms-2" id="addEditCancelBtn">Cancel</button>
            </div>
        </form>
    </div>
    {/if}
    <h3 class="mt-5 mb-3">Active Trailers</h3>
    <div class="table-responsive">
        <table class="table modern-table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Trailer ID</th>
                    {foreach $questions_first as $q}
                        <th>{$q.label|escape}</th>
                    {/foreach}
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                {foreach $active as $trl}
                    {assign var=answers value=$trl.answers}
                    <tr>
                        <td class="fw-semibold">{$trl.trl_id|escape}</td>
                        {foreach $trl.answers_first as $a}
                            <td>
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
                            <a href="maintenance.php?trl_id={$trl.id}&type=trailer" class="btn modern-btn modern-btn-info btn-sm">View Maintenance</a>
                            {if $is_admin}
                            <a href="trailer.php?archive={$trl.id}" class="btn modern-btn modern-btn-warning btn-sm">Archive</a>
                            <button class="btn modern-btn modern-btn-secondary btn-sm" onclick="editTrl({$trl.id}, '{$trl.answers_json}')">Edit</button>
                            {/if}
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    {if $is_admin}
    <h3 class="mt-5 mb-3">Archived Trailers</h3>
    <div class="table-responsive">
        <table class="table modern-table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Trailer ID</th>
                    {foreach $questions_first as $q}
                        <th>{$q.label|escape}</th>
                    {/foreach}
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                {foreach $archived as $trl}
                    {assign var=answers value=$trl.answers}
                    <tr>
                        <td class="fw-semibold">{$trl.trl_id|escape}</td>
                        {foreach $trl.answers_first as $a}
                            <td>
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
                            <a href="trailer.php?unarchive={$trl.id}" class="btn modern-btn modern-btn-success btn-sm">Unarchive</a>
                            <a href="maintenance.php?trl_id={$trl.id}" class="btn modern-btn modern-btn-info btn-sm">View Maintenance</a>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    {/if}
</div>