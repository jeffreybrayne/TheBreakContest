{jrCore_module_url module="jrAudio" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
        {if $item.chart_direction == 'up'}
            {if $item.chart_change > 10}
                {assign var="chart_image" value="hot_up"}
            {else}
                {assign var="chart_image" value="up"}
            {/if}
        {elseif $item.chart_direction == 'down'}
            {if $item.chart_change > 10}
                {assign var="chart_image" value="cool_down"}
            {else}
                {assign var="chart_image" value="down"}
            {/if}
        {elseif $item.chart_direction == 'same'}
            {assign var="chart_image" value="same"}
        {elseif $item.chart_direction == 'new'}
            {assign var="chart_image" value="new"}
        {/if}

        <div class="container">

            <div class="row">

                <div class="col1">
                    <div class="p5">
                        <div class="rank">
                            {$item.list_rank}<br>
                            {if $item.chart_direction != 'same'}
                                {jrCore_lang skin=$_conf.jrCore_active_skin id="98" default="moved" assign="chart_position_title1"}
                                {assign var="cp_title" value="`$chart_position_title1` `$item.chart_direction`"}
                            {else}
                                {jrCore_lang skin=$_conf.jrCore_active_skin id="65" default="position" assign="chart_position_title1"}
                                {assign var="cp_title" value="`$item.chart_direction` `$chart_position_title1`"}
                            {/if}
                            {jrCore_image image="chart_`$chart_image`.png" alt="`$item.chart_direction`" title=$cp_title}<br>
                            {if $item.chart_change > 0}
                                ({$item.chart_change})
                                {else}
                                (-)
                            {/if}
                        </div>
                    </div>
                </div>
                <div class="col1">
                    <div class="center">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="medium" crop="auto" class="iloutline img_scale" alt=$item.audio_title style="max-width:290px;"}</a>
                    </div>
                </div>
                <div class="col1">
                    <div class="p10">
                        {if $item.audio_file_extension == 'mp3'}
                            {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item}
                        {else}
                            {jrCore_lang skin=$_conf.jrCore_active_skin id="156" default="Download" assign="alttitle"}
                            <a href="{$jamroom_url}/{$murl}/download/audio_file/{$item._item_id}">{jrCore_image image="download.png" alt=$alttitle title=$alttitle}</a>
                        {/if}
                    </div>
                </div>
                <div class="col7">
                    <div class="p5">
                        <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{$item.audio_title}</a></h3><br>
                        <span class="sub_title">{jrCore_lang module="jrAudio" id="12" default="genre"}:</span> <span class="normal">{$item.audio_genre}</span> &nbsp; <span class="sub_title">{jrCore_lang skin=$_conf.jrCore_active_skin id="64" default="album"}:</span> <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.audio_album_url}"><span class="normal">{$item.audio_album}</span></a>
                        {jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$item._item_id current=$item.audio_rating_1_average_count|default:0 votes=$item.audio_rating_1_count|default:0 }
                    </div>
                </div>
                <div class="col2 last">
                    <div class="nowrap float-right">
                        {if jrCore_module_is_active('jrPlaylist')}
                            {jrCore_module_function function="jrPlaylist_button" image="playlist_add.png" playlist_for="jrAudio" item_id=$item._item_id}
                        {/if}
                        {jrCore_item_update_button module="jrAudio" profile_id=$item._profile_id item_id=$item._item_id}
                        {jrCore_item_delete_button module="jrAudio" profile_id=$item._profile_id item_id=$item._item_id}
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col12 last">
                    <div class="divider mb10 mt10"></div>
                </div>
            </div>

        </div>

    {/foreach}
{/if}
