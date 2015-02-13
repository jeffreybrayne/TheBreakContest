{* prev/next page profile footer links *}
{if $info.prev_page > 0 || $info.next_page > 0}
<div class="block">
    <table style="width:100%">
        <tr>
            <td style="width:25%">
            {if $info.prev_page > 0}
                {if isset($pager_load_id)}
                    <a onclick="jrCore_load_into('{$pager_load_id}','{$pager_load_url}/p={$info.prev_page}')">{jrCore_icon icon="previous"}</a>
                {else}
                    <a href="{$info.page_base_url}/p={$info.prev_page}">{jrCore_icon icon="previous"}</a>
                {/if}
            {/if}
            </td>
            <td style="width:50%;text-align:center">
                {if $info.total_pages <= 3}
                    {$info.page} &nbsp;/ {$info.total_pages}
                {else}
                    <form name="form" method="post" action="_self">
                    {if isset($pager_load_id)}
                        <select name="pagenum" class="form_select list_pager" style="width:60px;" onchange="jrCore_load_into('{$pager_load_id}','{$pager_load_url}/p=' + $(this).val());">
                    {else}
                        <select name="pagenum" class="form_select list_pager" style="width:60px;" onchange="window.location='{$info.page_base_url}/p=' + $(this).val();">
                    {/if}
                    {for $pages=1 to $info.total_pages}
                        {if $info.page == $pages}
                            <option value="{$info.this_page}" selected="selected"> {$info.this_page}</option>
                        {else}
                            <option value="{$pages}"> {$pages}</option>
                        {/if}
                    {/for}
                        </select>&nbsp;/&nbsp;{$info.total_pages}
                    </form>
                {/if}
            </td>
            <td style="width:25%;text-align:right">
            {if $info.next_page > 0}
                {if isset($pager_load_id)}
                    <a onclick="jrCore_load_into('{$pager_load_id}','{$pager_load_url}/p={$info.next_page}')">{jrCore_icon icon="next"}</a>
                {else}
                    <a href="{$info.page_base_url}/p={$info.next_page}">{jrCore_icon icon="next"}</a>
                {/if}
            {/if}
            </td>
        </tr>
    </table>
</div>
{/if}