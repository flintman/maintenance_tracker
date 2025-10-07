<div class="container py-4">
    <h1 class="display-5 fw-bold mb-4">Dashboard</h1>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card modern-card text-white mb-3 shadow" style="background: linear-gradient(90deg, #232526 0%, #00bcd4 100%)">
                <div class="card-body">
                    <h5 class="card-title">Trailers</h5>
                    <p class="card-text display-4">{$trailer_count|default:0}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card modern-card text-white mb-3 shadow" style="background: linear-gradient(90deg, #1976d2 0%, #00bcd4 100%)">
                <div class="card-body">
                    <h5 class="card-title">Refrigeration Units</h5>
                    <p class="card-text display-4">{$unit_count|default:0}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card modern-card text-white mb-3 shadow" style="background: linear-gradient(90deg, #00bcd4 0%, #232526 100%)">
                <div class="card-body">
                    <h5 class="card-title">Latest Maintenance</h5>
                    <ul class="list-group list-group-flush">
                        {foreach $latest_maintenance as $m}
                        <li class="list-group-item bg-transparent text-white">
                            <a href="view_maintenance.php?id={$m.id}&type={if $m.refrigeration_id}refrigeration{else}trailer{/if}" class="text-white text-decoration-none">
                                <strong>{$m.type_of_service|escape}</strong> -
                                {if $m.refrigeration_id}
                                    {$m.refrigeration_answer_1|escape} (Trailer {$m.refrigeration_trailer_id|escape})
                                {else}
                                    {if $m.trailer_id}{$m.trailer_id|escape}{elseif $m.trl_id}{$m.trl_id|escape}{else}Unknown{/if}
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
</div>
