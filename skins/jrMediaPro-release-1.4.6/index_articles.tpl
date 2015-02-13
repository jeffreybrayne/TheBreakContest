{capture name="row_template" assign="index_articles_row"}
    {literal}
        {if isset($_items)}
            {foreach from=$_items item="item"}
                <div class="body_5 page" style="padding:5px; margin-bottom:5px; margin-right:10px;">
                    <h3 style="font-weight:normal;">
                        {if $item.page_location == '0'}
                            <a href="{$jamroom_url}/page/{$item._item_id}/{$item.page_title_url}">{if strlen($item.page_title) > 35}{$item.page_title|truncate:35:"...":false}{else}{$item.page_title}{/if}</a></h3>
                        {else}
                            <a href="{jrProfile_item_url module="jrPage" profile_url=$item.profile_url item_id=$item._item_id title=$item.page_title}">{if strlen($item.page_title) > 35}{$item.page_title|truncate:35:"...":false}{else}{$item.page_title}{/if}</a></h3>
                        {/if}
                    <div style="font-size:12px;">{$item._created|jrCore_format_time}</div>
                    <div style="font-size:11px;"><span class="highlight-txt">By:</span>&nbsp;<span class="capital">{$item.profile_name}</span></div>
                    {if jrCore_module_is_active('jrComment')}
                        <br>
                        <div class="float-right" style="padding-right:5px;">
                            {if $item.page_location == '0'}
                            <a href="{$jamroom_url}/page/{$item._item_id}/{$item.page_title_url}"><span class="capital">{jrCore_lang module="jrBlog" id="27" default="comments"}:</span> {$item.blog_comment_count|default:0}</a><br>
                            {else}
                            <a href="{jrProfile_item_url module="jrPage" profile_url=$item.profile_url item_id=$item._item_id title=$item.page_title}#comments"><span class="capital">{jrCore_lang module="jrBlog" id="27" default="comments"}</span>:  {$item.page_comment_count|default:0}</a><br>
                            {/if}
                        </div>
                        <div class="clear"></div>
                    {/if}
                </div>
            {/foreach}
        {/if}
    {/literal}
{/capture}

{jrCore_list module="jrPage" order_by="_created desc" limit="5" template=$index_articles_row}
