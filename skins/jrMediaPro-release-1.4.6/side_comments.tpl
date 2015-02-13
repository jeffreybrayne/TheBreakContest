{if isset($_items)}
<div class="body_3 mb20">
    <div class="body_3_title">
        <div class="title_2">{jrCore_lang skin=$_conf.jrCore_active_skin id="78" default="Latest"} {jrCore_lang skin=$_conf.jrCore_active_skin id="77" default="Comments"}</div>
    </div>
    <div style="max-height:450px;overflow:auto;">
        {foreach from=$_items item="item"}
            {jrCore_module_url module=$item.comment_module assign="curl"}
            <div class="block">
                <div style="display:table">
                    <div style="display:table-row;">
                        <div style="display:table-cell;width:1%;text-align:center;vertical-align:top;">
                            <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="xxsmall" crop="auto" alt=$item.user_name class="action_item_user_img iloutline"}</a>
                        </div>
                        <div style="display:table-cell;text-align:left;vertical-align:top;padding-left:5px;">
                            <div class="normal">
                                <a href="{$item.comment_url}">Re:&nbsp;{$item.comment_item_title|truncate:20:"...":false}</a><br>
                                {$item._created|jrCore_date_format}<br>
                                <span class="captial bold"><i>{jrCore_lang skin=$_conf.jrCore_active_skin id="112" default="By"}:</i></span>&nbsp;<a href="{$jamroom_url}/{$item.profile_url}"><span class="capital">{$item.profile_name|truncate:20:"...":false}</span></a><br>
                                <br>
                                {$item.comment_text|truncate:200:"...":false|jrCore_strip_html}
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
        {/foreach}
    </div>
</div>
{/if}
