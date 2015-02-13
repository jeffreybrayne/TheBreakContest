{if isset($_items)}
    {foreach from=$_items item="item"}
        <div class="body_5" style="margin-right:auto;">
            <div class="container">
                <div class="row">

                    <div class="col1">
                        <div class="rank mobile" style="font-size:24px;vertical-align:middle;padding-top:50px;">
                            {$item.list_rank}&nbsp;
                        </div>
                    </div>
                    <div class="col2">
                        <div class="center middle">
                            <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="medium" crop="auto" class="iloutline img_shadow img_scale" alt=$item.profile_name title=$item.profile_name style="max-width:190px;margin-bottom:10px;"}</a><br>
                        </div>
                    </div>
                    <div class="col9 last">
                        <div class="left" style="padding-left:15px;">
                            <span class="capital bold">{jrCore_lang skin=$_conf.jrCore_active_skin id="121" default="Name"}</span>: <a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name}"><span class="capital bold">{$item.profile_name}</span></a><br>
                            {if isset($item.profile_influences) && strlen($item.profile_influences) > 0}
                                <span class="capital bold">{jrCore_lang skin=$_conf.jrCore_active_skin id="143" default="Influences"}</span>: <span class="hl-2">{if isset($item.profile_influences) && strlen($item.profile_influences) > 70}{$item.profile_influences|truncate:70:"...":true}&nbsp;And more!!!{else}{$item.profile_influences}{/if}</span><br>
                            {/if}
                            <span class="capital bold">{jrCore_lang skin=$_conf.jrCore_active_skin id="50" default="Views"}</span>: <span class="hl-3">{$item.profile_view_count}</span></a><br>
                            {if !isset($item.profile_influences) || strlen($item.profile_influences) == 0}
                                <br>
                            {/if}
                            {if isset($item.profile_bio) && strlen($item.profile_bio) > 0}
                                <span class="hl-4 bold">{jrCore_lang skin=$_conf.jrCore_active_skin id="118" default="About"}:</span><br>
                                {$item.profile_bio|truncate:106:"...":false|jrCore_format_string:$item.profile_quota_id|nl2br}<br>
                            {else}
                                <br><br><br>
                            {/if}
                            {jrCore_list module="jrAudio" order_by="_created desc" limit="1" search1="_profile_id = `$item._profile_id`" template="index_top_artists_song.tpl"}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    {/foreach}
{/if}

