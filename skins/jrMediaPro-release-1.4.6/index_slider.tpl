{if isset($_items)}
    {jrCore_module_url module="jrProfile" assign="murl"}
    {foreach from=$_items item="item"}
        <li>
            <div class="container">
                <div class="row">
                    <div class="col3">
                        <div class="fleximage">
                            <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="xxlarge" crop="square" alt=$item.profile_name title=$item.profile_name class="img_shadow img_scale"}</a>
                        </div>
                    </div>
                    <div class="col9 last">
                        <div class="fav_body ml20">
                            <div class="flex-caption">
                                <div class="flex-caption-content">
                                    <div  class="slidetext2" style="padding:0;margin:0;">
                                        <h1><a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a></h1><br>
                                        <br>
                                        {jrCore_list module="jrAudio" order_by="_created desc" limit="2" search1="_profile_id = `$item._profile_id`" template="index_slider_song.tpl"}<br>
                                        <span class="capital bold">{jrCore_lang skin=$_conf.jrCore_active_skin id="143" default="Influences"}:</span>&nbsp;<span class="hl-1">{if isset($item.profile_influences) && strlen($item.profile_influences) > 70}{$item.profile_influences|truncate:70:"...":true}&nbsp;And more!!!{else}{$item.profile_influences}{/if}</span><br>
                                        <br>
                                        <div class="mobile">
                                            <span class="hl-4 bold">{jrCore_lang skin=$_conf.jrCore_active_skin id="118" default="About"}:</span><br>
                                            {$item.profile_bio|truncate:260:"...":false|jrCore_format_string:$item.profile_quota_id}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
    {/foreach}
{/if}
