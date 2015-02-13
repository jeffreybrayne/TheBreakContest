<div style="width:90%;display:table;margin:0 auto;">

    {capture name="template" assign="stats_tpl"}
        {literal}
            {foreach $_stats as $title => $_stat}
            <div style="display:table-row">
                <div class="capital bold" style="display:table-cell">{$title}</div>
                <div class="hl-3" style="width:5%;display:table-cell;text-align:right;">{$_stat.count}</div>
            </div>
            {/foreach}
        {/literal}
    {/capture}

    {jrCore_stats template=$stats_tpl}

</div>
