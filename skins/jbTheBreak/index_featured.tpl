{if isset($_items)}
    {jrCore_module_url module="jrProfile" assign="murl"}
    <div class="container">
        {if $info.total_pages > 1}
            <div class="row">
                <div class="col12 last">
                    <div class="page mb10">
                        {if $info.prev_page > 0}
                            <div class="float-left">
                                <a onclick="jrLoad('#featured_artists','{$jamroom_url}/index_featured_list/p={$info.prev_page}');">{jrCore_icon icon="arrow-left"}</a>
                            </div>
                        {/if}
                        {if $info.next_page > 1}
                            <div class="float-right">
                                <a onclick="jrLoad('#featured_artists','{$jamroom_url}/index_featured_list/p={$info.next_page}');">{jrCore_icon icon="arrow-right"}</a>
                            </div>
                        {/if}
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        {/if}
        {foreach from=$_items item="item"}
            <div class="row">
                <div class="col8">
                    <div class="p5">
                        <h2><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name}">{$item.profile_name}</a></h2><br>
                        <br>
                        {$item.profile_bio|truncate:220:"...":false|jrCore_format_string:$item.profile_quota_id|nl2br}<br>
                        <br>
                        {jrCore_list module="jrAudio" order_by="_created desc" limit="1" search1="_profile_id = `$item._profile_id`" template="index_featured_song.tpl"}
                    </div>
                </div>
                <div class="col4 last">
                    <div class="featured_img">
                        <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="xxlarge" crop="height" height="250" alt=$item.profile_name title=$item.profile_name class="iloutline img_shadow img_scale" style="max-height:250px;"}</a><br>
                    </div>
                </div>
            </div>

        {/foreach}
    </div>
{/if}

