{if isset($jrFeed.feed)}
<div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="78" default="Latest"} {jrCore_lang skin=$_conf.jrCore_active_skin id="77" default="Comments"}</div>
<div class="body_3 mb20">
    <div style="height:350px;overflow:auto;">
        <div class="normal" style="text-align:center;">{$jrFeed.feed.description}</div>
        {if isset($jrFeed.feed.item)}
            <div class="block">
                {foreach from=$jrFeed.feed.item item="item"}
                    <div class="normal">
                        <a href="{$item.link}">{$item.title}</a><br>
                        {$item.pubDate|jrCore_date_format}<br>
                        {$item.description}<br>
                    </div>
                    <hr>
                {/foreach}
            </div>
        {/if}
    </div>
</div>
{/if}

