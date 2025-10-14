{* Dashboard Template *}
<div class="container mt-5">
    <h2 class="mb-4">Dashboard</h2>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title">{$primary_label|escape} Units</h5>
                    <p class="card-text display-4">{$primary_count}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title">{$secondary_label|escape} Units</h5>
                    <p class="card-text display-4">{$unit_count}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title">Latest Maintenance</h5>
                    <ul class="list-group list-group-flush">
                        {foreach $latest_maintenance as $m}
                        <li class="list-group-item bg-info text-white">
                            <a href="view_maintenance.php?source=dashboard&id={$m.id}&type={if $m.secondary_id}secondary_units{else}primary{/if}" class="text-white text-decoration-none">
                                <strong>{$m.type_of_service|escape}</strong> -
                                {if $m.secondary_id}
                                    {$m.secondary_answer_1|escape} ({$primary_label|escape} {$m.secondary_primary_id|escape})
                                {else}
                                    {if $m.primary_id}{$m.primary_id|escape}{elseif $m.pmy_id}{$m.pmy_id|escape}{else}Unknown{/if}
                                {/if}
                                <br>
                                <small>{$m.performed_at|escape} by {$m.performed_by|escape}</small>
                            </a>
                        </li>
                        {/foreach}
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Message Board</h5>
                </div>
                <div class="card-body">
                    {$message_board|escape}
                </div>
            </div>
        </div>
    </div>
</div>
