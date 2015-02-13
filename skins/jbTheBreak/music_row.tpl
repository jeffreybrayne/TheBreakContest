<h1>Params: {$_user.profile_quota_id}</h1>

{if isset($_items)}
    {jrCore_module_url module="jrAudio" assign="murl"}
    {foreach from=$_items item="item"}
    {if $item.profile_quota_id == $_user.profile_quota_id}
        <div class="body_5 page" style="margin-right:auto;">

            <div class="container">

                <div class="row">

                    <div class="col1">
                        <div class="center">
                            {if isset($_post.option) && $_post.option == 'by_album'}
                                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.audio_album_url}">
                                	{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="medium" crop="auto" class="iloutline img_scale" alt=$item.audio_title style="max-width:196px;"}
                                	<!-- {jrCore_image module="jrImage" image="Audio_Icon.jpg" crop="auto" class="iloutline img_scale" alt=$item.audio_title style="max-width:196px;"} -->
                                </a>
                            {else}
                                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">
                                	{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="medium" crop="auto" class="iloutline img_scale" alt=$item.audio_title style="max-width:196px;"}
                                	<!-- {jrCore_image module="jrImage" image="Audio_Icon.jpg" crop="auto" class="iloutline img_scale" alt=$item.audio_title style="max-width:196px;"} -->
                                </a>
                            {/if}
                        </div>
                    </div>
                    <div class="col1">
                        <div class="p10 middle">
                            {if $item.audio_file_extension == 'mp3'}
                                {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item image="button_player"}
                            {else}
                                {jrCore_lang skin=$_conf.jrCore_active_skin id="156" default="Download" assign="alttitle"}
                                <a href="{$jamroom_url}/{$murl}/download/audio_file/{$item._item_id}">{jrCore_image image="download.png" alt=$alttitle title=$alttitle}</a>
                            {/if}
                        </div>
                    </div>
                    <div class="col8">
                        <div class="p5 middle">
                        {if isset($_post.option) && $_post.option == 'by_album'}
                            <h3 class="media_title"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.audio_album_url}"><span class="capital">{$item.audio_album}</span></a></h3>&nbsp;-&nbsp;<h4><a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a></h4><br>
                        {else}
                            <h3 class="media_title"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{$item.audio_title}</a></h3>&nbsp;-&nbsp;<h4><a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a></h4><br>
                        {/if}
                            {if isset($item.audio_genre) && strlen($item.audio_genre) >0}<span class="capital bold">{jrCore_lang module="jrAudio" id="12" default="genre"}</span>: <span class="hl-4 bold">{$item.audio_genre}</span>&nbsp;{/if}
                            {if isset($item.audio_album) && strlen($item.audio_album) > 0 && $_post.option != 'by_album'}<span class="capital bold">{jrCore_lang skin=$_conf.jrCore_active_skin id="64" default="album"}</span>: <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.audio_album_url}"><span class="capital bold">{$item.audio_album}</span></a><br>{else}<br>{/if}
                            {if isset($_post.option) && $_post.option == 'by_plays'}
                                <span class="capital bold">{jrCore_lang skin=$_conf.jrCore_active_skin id="51" default="plays"}:</span> <span class="hl-3">{$item.audio_file_stream_count}</span><br>
                            {/if}
                            {jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$item._item_id current=$item.audio_rating_1_average_count|default:0 votes=$item.audio_rating_1_count|default:0 }
                        </div>
                    </div>
                    <div class="col2 last">
                        <div class="nowrap float-right">
                            {if isset($_post.option) && $_post.option == 'by_album'}
                                {jrFoxyCartBundle_get_album module="jrAudio" profile_id=$item._profile_id name=$item.audio_album assign="album"}
                                {if isset($album.bundle_item_price) && $album.bundle_item_price > 0}
                                    {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrFoxyCartBundle" field="bundle" quantity_max="1" price=$album.bundle_item_price no_bundle="true" item=$album}
                                {/if}
                            {else}
                                {if isset($item.audio_file_item_price) && $item.audio_file_item_price > 0}
                                    {if jrCore_module_is_active('jrFoxyCart')}
                                        {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrAudio" field="audio_file" item=$item}
                                    {elseif jrCore_module_is_active('jrPayPal')}
                                        {jrPayPal_buy_now_button module="jrAudio" item=$item}
                                    {/if}
                                {elseif isset($item.audio_bundle_only) && $item.audio_bundle_only == 'on'}
                                    {jrFoxyCartBundle_get_album module="jrAudio" profile_id=$item._profile_id name=$item.audio_album assign="album"}
                                    {if isset($album.bundle_item_price) && $album.bundle_item_price > 0}
                                        {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrFoxyCartBundle" field="bundle" quantity_max="1" price=$album.bundle_item_price no_bundle="true" item=$album}
                                    {else}
                                        <div class="add_to_cart_section" title="Not Available"><span class="add_to_cart_price">N/A</span>{jrCore_icon icon="lock" size="18"}</div>
                                    {/if}
                                {elseif $_conf.jrAudio_block_download != 'on' && $item.audio_bundle_only != 'on'}
                                    <div class="add_to_cart_section"><span class="add_to_cart_price">Free</span><a href="{$jamroom_url}/{$murl}/download/audio_file/{$item._item_id}">{jrCore_icon icon="download" size="18"}</a></div>
                                {else}
                                    <div class="add_to_cart_section" title="Download Not Available"><span class="add_to_cart_price">N/A</span>{jrCore_icon icon="lock" size="18"}</div>
                                {/if}
                            {/if}
                            {jrCore_module_function function="jrPlaylist_button" playlist_for="jrAudio" item_id=$item._item_id}
                        </div>
                    </div>

                </div>

            </div>

        </div>
	{/if}
    {/foreach}
    {if $info.total_pages > 1}
        <div class="block">
            <table style="width:100%;">
                <tr>

                    <td class="body_4 p5 middle" style="width:25%;text-align:center;">
                        {if isset($info.prev_page) && $info.prev_page > 0}
                            {if isset($_post.search_area) && $_post.search_area == 'audio_genre'}
                                <a href="{$info.page_base_url}/search_area={$_post.search_area}/search_string={$_post.search_string}/p={$info.prev_page}"><span class="button-arrow-previous">&nbsp;</span></a>
                            {else}
                                <a href="{$info.page_base_url}/p={$info.prev_page}"><span class="button-arrow-previous">&nbsp;</span></a>
                            {/if}
                        {else}
                            <span class="button-arrow-previous-off">&nbsp;</span>
                        {/if}
                    </td>

                    <td class="body_4 p5 middle" style="width:50%;text-align:center;border:1px solid #282828;">
                        {if $info.total_pages <= 2 || $info.total_pages > 500}
                            {$info.page} &nbsp;/ {$info.total_pages}
                        {else}
                            <form name="form" method="post" action="_self">
                            {if isset($_post.search_area) && $_post.search_area == 'audio_genre'}
                                <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;window.location='{$info.page_base_url}/search_area={$_post.search_area}/search_string={$_post.search_string}/p=' +sel">
                            {else}
                                <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;window.location='{$info.page_base_url}/p=' +sel">
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

                    <td class="body_4 p5 middle" style="width:25%;text-align:center;">
                        {if isset($info.next_page) && $info.next_page > 1}
                            {if isset($_post.search_area) && $_post.search_area == 'audio_genre'}
                                <a href="{$info.page_base_url}/search_area={$_post.search_area}/search_string={$_post.search_string}/p={$info.next_page}"><span class="button-arrow-next">&nbsp;</span></a>
                            {else}
                                <a href="{$info.page_base_url}/p={$info.next_page}"><span class="button-arrow-next">&nbsp;</span></a>
                            {/if}
                        {else}
                            <span class="button-arrow-next-off">&nbsp;</span>
                        {/if}
                    </td>

                </tr>
            </table>
        </div>
    {/if}
{/if}
