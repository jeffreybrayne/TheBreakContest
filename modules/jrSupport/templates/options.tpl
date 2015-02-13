<div class="item">
    {if isset($market_title)}
    <div style="display:table;width:100%">
        <div style="display:table-row">
            <div class="p5" style="display:table-cell;width:100%;vertical-align:top">
                <h2>{$market_title}</h2> &nbsp; by <a href="{$profile_url}" target="_blank">@{$profile_url|basename}</a>
            </div>
        </div>
    </div>
    <div style="display:table;width:100%">
        <div style="display:table-row;">
            <div class="p5" style="display:table-cell;width:10%;vertical-align:top;padding-left:12px">
                <img src="{$market_icon}" alt="{$market_name}" width="64">
            </div>
            <div style="display:table-cell;width:20%;vertical-align:middle;height:64px;padding-right:18px;">
                <div class="p10 success rounded" style="vertical-align:top;text-align:center;border:1px solid #ccc">
                    <a href="{$forum_url}" target="_blank"><h3>Support Forum</h3></a>
                </div>
            </div>
            {if isset($priority_url)}
            <div style="display:table-cell;width:20%;vertical-align:middle;height:64px;padding-right:18px;">
                <div class="p10 success rounded" style="vertical-align:top;text-align:center;border:1px solid #ccc">
                    <a href="{$priority_url}" target="_blank"><h3>Priority Ticket</h3></a>
                </div>
            </div>
            {/if}
            {if isset($market_url)}
            <div style="display:table-cell;width:20%;vertical-align:middle;height:64px;padding-right:18px;">
                <div class="p10 success rounded" style="vertical-align:top;text-align:center;border:1px solid #ccc">
                    <a href="{$market_url}" target="_blank"><h3>Product Detail</h3></a>
                </div>
            </div>
            {/if}
            <div style="display:table-cell;width:20%;vertical-align:middle;height:64px;padding-right:12px;">
                <div class="p10 success rounded" style="vertical-align:top;text-align:center;border:1px solid #ccc">
                    <a href="{$documentation_url}" target="_blank"><h3>Documentation</h3></a>
                </div>
            </div>

        </div>
    </div>
    {else}
    <div class="p10 error rounded" style="width:80%;test-align:center">
        No support information available for the selected item
    </div>
    {/if}

</div>
