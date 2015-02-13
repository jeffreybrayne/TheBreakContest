{jrCore_module_url module="jrAudio" assign="murl"}
{if isset($_items)}

    {foreach from=$_items item="item"}

    <div class="item">

        <div class="container">
            <div class="row">
                <div class="col2">
                    <div class="block_image">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">
                        	<!-- {jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="xlarge" crop="auto" class="iloutline img_scale" alt=$item.audio_title width=false height=false} -->
                        	{jrCore_image module="jrImage" image="Audio_Icon.jpg" crop="auto" class="iloutline img_scale"}
                        </a>
                    </div>
                </div>
                <div class="col1">
                    <div class="p5">
                    {if $item.audio_active == 'on' && $item.audio_file_extension == 'mp3'}
                        {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item}
                    {else}
                        &nbsp;
                    {/if}
                    </div>
                </div>
                <div class="col4">
                    <div class="p5" style="overflow-wrap:break-word">
                        <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{$item.audio_title}</a></h3><br>
                        <span class="info">{jrCore_lang module="jrAudio" id="31" default="album"}:</span> <span class="info_c"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.audio_album_url}">{$item.audio_album}</a></span><br>
                        <span class="info">{jrCore_lang module="jrAudio" id="12" default="genre"}:</span> <span class="info_c">{$item.audio_genre}</span><br>
                        {jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$item._item_id current=$item.audio_rating_1_average_count|default:0 votes=$item.audio_rating_1_count|default:0}
                    </div>
                </div>
                <div class="col5 last">
                    <div class="block_config">

                        {jrCore_item_list_buttons module="jrAudio" field="audio_file" item=$item}

                    </div>
                </div>
            </div>

        </div>
    </div>
    {/foreach}
{/if}