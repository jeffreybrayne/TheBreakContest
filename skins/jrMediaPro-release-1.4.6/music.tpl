{assign var="selected" value="music"}
{assign var="spt" value="music"}
{assign var="no_inner_div" value="true"}
{if isset($_post.search_area) && $_post.search_area == 'audio_genre'}
    {jrCore_lang skin=$_conf.jrCore_active_skin id="56" default="Music" assign="page_title1"}
    {assign var="page_title" value="`$_post.search_string` `$page_title1`"}
{else}
    {jrCore_lang skin=$_conf.jrCore_active_skin id="56" default="Music" assign="page_title"}
{/if}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrSetActive('#default');
         });
</script>

<div class="container">
    <div class="row">

        <div class="col9">

            <div class="body_1 mr5">

                <div class="container">
                    <div class="row">
                        <div class="col12 last">
                            <h1>{if isset($_post.search_area) && $_post.search_area == 'audio_genre'}{$_post.search_string}&nbsp;{/if}{jrCore_lang skin=$_conf.jrCore_active_skin id="56" default="music"}</h1>
                            <div class="body_1">
                                <div class="br-info capital" style="margin-bottom:10px;">

                                    {if !isset($_post.option) || $_post.option == 'by_newest'}
                                    {assign var="order_by" value="_created desc"}
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;
                                    {else}
                                    <a href="{$jamroom_url}/music/by_newest{if isset($_post._1) && strlen($_post._1) > 0}/{$_post._1}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}</a>&nbsp;
                                    {/if}

                                    <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;

                                    {if isset($_post.option) && $_post.option == 'by_plays'}
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="59" default="by plays"}
                                    {assign var="order_by" value="audio_file_stream_count NUMERICAL_DESC"}
                                    {else}
                                    <a href="{$jamroom_url}/music/by_plays{if isset($_post._1) && strlen($_post._1) > 0}/{$_post._1}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="59" default="by plays"}</a>
                                    {/if}

                                    {if jrCore_module_is_active('jrRating')}
                                    <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;

                                    {if isset($_post.option) && $_post.option == 'by_ratings'}
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="60" default="by rating"}
                                    {assign var="order_by" value="audio_rating_1_average_count NUMERICAL_DESC"}
                                    {else}
                                    <a href="{$jamroom_url}/music/by_ratings{if isset($_post._1) && strlen($_post._1) > 0}/{$_post._1}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="60" default="by rating"}</a>
                                    {/if}
                                    {/if}

                                    <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;

                                    {if isset($_post.option) && $_post.option == 'by_album'}
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="185" default="By Album"}
                                    {assign var="order_by" value="audio_album asc"}
                                    {else}
                                    <a href="{$jamroom_url}/music/by_album{if isset($_post._1) && strlen($_post._1) > 0}/{$_post._1}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="185" default="By Album"}</a>
                                    {/if}

                                </div>

                                &raquo;&nbsp;

                                {* prep our array for looping through and constructing our letter chooser *}
                                {jrCore_array name="alpha" value=$_conf.jrMediaPro_letter_alphabet explode="true" separator=","}
                                {foreach from=$alpha item="char"}
                                    <a href="{$jamroom_url}/music{if isset($_post.option) && strlen($_post.option) > 0}/{$_post.option}{else}/by_newest{/if}/{$char}"><span class="capital">{$char}</span></a>&nbsp;
                                {/foreach}
                                &laquo;&nbsp;<a href="{$jamroom_url}/music{if isset($_post.option) && strlen($_post.option) > 0}/{$_post.option}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
                                <br><br>

                                {if isset($_conf.jrMediaPro_require_images) && $_conf.jrMediaPro_require_images == 'on'}
                                    {if isset($_post.search_area) && $_post.search_area == 'audio_genre'}
                                        {jrCore_list module="jrAudio" order_by=$order_by search1="audio_genre like `$_post.search_string`" template="music_row.tpl" require_image="audio_image" pagebreak=$_conf.jrMediaPro_default_pagebreak page=$_post.p}
                                    {elseif !empty($_post._1)}
                                        {if isset($_post.option) && $_post.option == 'by_album'}
                                            {jrCore_list module="jrAudio" order_by=$order_by group_by="audio_album_url" template="music_row.tpl" require_image="audio_image" search="audio_album like `$_post._1`%" pagebreak=$_conf.jrMediaPro_default_pagebreak page=$_post.p}
                                        {else}
                                            {jrCore_list module="jrAudio" order_by=$order_by template="music_row.tpl" require_image="audio_image" search="audio_title like `$_post._1`%" pagebreak=$_conf.jrMediaPro_default_pagebreak page=$_post.p}
                                        {/if}
                                    {else}
                                        {if isset($_post.option) && $_post.option == 'by_album'}
                                            {jrCore_list module="jrAudio" order_by=$order_by group_by="audio_album_url" template="music_row.tpl" require_image="audio_image" pagebreak=$_conf.jrMediaPro_default_pagebreak page=$_post.p}
                                        {else}
                                            {jrCore_list module="jrAudio" order_by=$order_by template="music_row.tpl" require_image="audio_image" pagebreak=$_conf.jrMediaPro_default_pagebreak page=$_post.p}
                                        {/if}
                                    {/if}
                                {else}
                                    {if isset($_post.search_area) && $_post.search_area == 'audio_genre'}
                                        {jrCore_list module="jrAudio" order_by=$order_by search1="audio_genre like `$_post.search_string`" template="music_row.tpl" pagebreak=$_conf.jrMediaPro_default_pagebreak page=$_post.p}
                                    {elseif !empty($_post._1)}
                                        {if isset($_post.option) && $_post.option == 'by_album'}
                                            {jrCore_list module="jrAudio" order_by=$order_by group_by="audio_album_url" template="music_row.tpl" search="audio_album like `$_post._1`%" pagebreak=$_conf.jrMediaPro_default_pagebreak page=$_post.p}
                                        {else}
                                            {jrCore_list module="jrAudio" order_by=$order_by template="music_row.tpl" search="audio_title like `$_post._1`%" pagebreak=$_conf.jrMediaPro_default_pagebreak page=$_post.p}
                                        {/if}
                                    {else}
                                        {if isset($_post.option) && $_post.option == 'by_album'}
                                            {jrCore_list module="jrAudio" order_by=$order_by group_by="audio_album_url" template="music_row.tpl" pagebreak=$_conf.jrMediaPro_default_pagebreak page=$_post.p}
                                        {else}
                                            {jrCore_list module="jrAudio" order_by=$order_by template="music_row.tpl" pagebreak=$_conf.jrMediaPro_default_pagebreak page=$_post.p}
                                        {/if}
                                    {/if}
                                {/if}

                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <div class="col3 last">
            <div class="body_1">
                {jrCore_include template="side_music.tpl"}
            </div>
        </div>

    </div>
</div>

{jrCore_include template="footer.tpl"}
