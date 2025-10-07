{include file="theme_2/header.tpl"}
<div class="container py-4">
    <h1 class="display-6 mb-4">View Maintenance Record</h1>
    {if isset($msg)}<div class="alert alert-info">{$msg}</div>{/if}
    <div class="card modern-card mb-4">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9">{$record.id}</dd>
                <dt class="col-sm-3">Title</dt>
                <dd class="col-sm-9">{$record.title}</dd>
                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">{$record.status}</dd>
                <dt class="col-sm-3">Assigned To</dt>
                <dd class="col-sm-9">{$record.assigned_to|default:'-'}</dd>
                <dt class="col-sm-3">Date</dt>
                <dd class="col-sm-9">{$record.date}</dd>
                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9">{$record.description|default:'-'}</dd>
            </dl>
        </div>
    </div>
    <a href="maintenance.php" class="btn modern-btn modern-btn-primary">Back to Maintenance</a>
</div>
{include file="theme_2/footer.tpl"}
