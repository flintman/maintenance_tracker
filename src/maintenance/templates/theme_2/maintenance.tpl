{include file="theme_2/header.tpl"}
<div class="container py-4">
    <h1 class="display-6 mb-4">Maintenance Records</h1>
    {if isset($msg)}<div class="alert alert-info">{$msg}</div>{/if}
    <div class="card modern-card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3 align-items-center">
                <div class="col-auto">
                    <input type="text" name="search" class="form-control" placeholder="Search maintenance..." value="{$search|default:''}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn modern-btn modern-btn-primary">Search</button>
                </div>
            </form>
        </div>
    </div>
    <table class="table modern-table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Status</th>
                <th>Assigned To</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            {foreach $records as $record}
            <tr>
                <td>{$record.id}</td>
                <td>{$record.title}</td>
                <td><span class="modern-badge">{$record.status}</span></td>
                <td>{$record.assigned_to|default:'-'}</td>
                <td>{$record.date}</td>
                <td>
                    <a href="view_maintenance.php?id={$record.id}" class="btn btn-sm btn-outline-primary">View</a>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
    {if $records|@count == 0}
    <div class="alert alert-secondary">No maintenance records found.</div>
    {/if}
</div>
{include file="theme_2/footer.tpl"}
