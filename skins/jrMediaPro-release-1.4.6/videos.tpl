{assign var="selected" value="videos"}
{assign var="spt" value="video"}
{assign var="no_inner_div" value="true"}
{if isset($_post.search_area) && $_post.search_area == 'video_category'}
    {jrCore_lang skin=$_conf.jrCore_active_skin id="14" default="Videos" assign="page_title1"}
    {assign var="page_title" value="`$page_title1` - `$_post.search_string`"}
{else}
    {jrCore_lang skin=$_conf.jrCore_active_skin id="14" default="Videos" assign="page_title"}
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
                            <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="14" default="videos"}{if isset($_post.search_area) && $_post.search_area == 'video_category'}&nbsp;-&nbsp;{$_post.search_string}{/if}</h2>
                            <div class="body_1">
                                <div class="br-info capital" style="margin-bottom:10px;">
                                    {if !isset($_post.option) || $_post.option == 'by_newest'}
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;
                                    {assign var="order_by" value="_created desc"}
                                    {else}
                                    <a href="{$jamroom_url}/videos/by_newest{if isset($_post._1) && strlen($_post._1) > 0}/{$_post._1}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}</a>&nbsp;
                                    {/if}

                                    <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;

                                    {if isset($_post.option) && $_post.option == 'by_plays'}
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="59" default="by plays"}
                                    {assign var="order_by" value="video_file_stream_count NUMERICAL_DESC"}
                                    {else}
                                    <a href="{$jamroom_url}/videos/by_plays{if isset($_post._1) && strlen($_post._1) > 0}/{$_post._1}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="59" default="by plays"}</a>
                                    {/if}

                                    {if jrCore_module_is_active('jrRating')}
                                    <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;

                                    {if isset($_post.option) && $_post.option == 'by_ratings'}
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="60" default="by rating"}
                                    {assign var="order_by" value="video_rating_1_average_count NUMERICAL_DESC"}
                                    {else}
                                    <a href="{$jamroom_url}/videos/by_ratings{if isset($_post._1) && strlen($_post._1) > 0}/{$_post._1}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="60" default="by rating"}</a>
                                    {/if}
                                    {/if}

                                    <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;

                                    {if isset($_post.option) && $_post.option == 'by_album'}
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="185" default="By Album"}
                                    {assign var="order_by" value="video_album asc"}
                                    {else}
                                    <a href="{$jamroom_url}/videos/by_album{if isset($_post._1) && strlen($_post._1) > 0}/{$_post._1}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="185" default="By Album"}</a>
                                    {/if}

                                </div>
                                &raquo;&nbsp;
                                {* prep our array for looping through and constructing our letter chooser *}
                                {jrCore_array name="alpha" value=$_conf.jrMediaPro_letter_alphabet explode="true" separator=","}
                                {foreach from=$alpha item="char"}
                                    <a href="{$jamroom_url}/videos{if isset($_post.option) && strlen($_post.option) > 0}/{$_post.option}{else}/by_newest{/if}/{$char}"><span class="capital">{$char}</span></a>&nbsp;
                                {/foreach}
                                &laquo;&nbsp;<a href="{$jamroom_url}/videos{if isset($_post.option) && strlen($_post.option) > 0}/{$_post.option}{/if}"><span class="capital">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</span></a>
                                <br><br>

                                {if isset($_conf.jrMediaPro_require_images) && $_conf.jrMediaPro_require_images == 'on'}
                                    {if isset($_post.search_area) && $_post.search_area == 'video_category'}
                                        {jrCore_list module="jrVideo" order_by=$order_by template="videos_row.tpl" search="video_category like `$_post.search_string`" require_image="video_image" pagebreak=$_conf.jrMediaPro_default_pagebreak page=$_post.p}
                                    {elseif !empty($_post._1)}
                                        {if isset($_post.option) && $_post.option == 'by_album'}
                                            {jrCore_list module="jrVideo" order_by=$order_by group_by="video_album_url" template="videos_row.tpl" search="video_album like `$_post._1`%" require_image="video_image" pagebreak=$_conf.jrMediaPro_default_pagebreak page=$_post.p}
                                        {else}
                                            {jrCore_list module="jrVideo" order_by=$order_by template="videos_row.tpl" search="video_title like `$_post._1`%" require_image="video_image" pagebreak=$_conf.jrMediaPro_default_pagebreak page=$_post.p}
                                        {/if}
                                    {else}
                                        {if isset($_post.option) && $_post.option == 'by_album'}
                                            {jrCore_list module="jrVideo" order_by=$order_by group_by="video_album_url" template="videos_row.tpl" require_image="video_image" pagebreak=$_conf.jrMediaPro_default_pagebreak page=$_post.p}
                                        {else}
                                            {jrCore_list module="jrVideo" order_by=$order_by template="videos_row.tpl" require_image="video_image" pagebreak=$_conf.jrMediaPro_default_pagebreak page=$_post.p}
                                        {/if}
                                    {/if}
                                {else}
                                    {if isset($_post.search_area) && $_post.search_area == 'video_category'}
                                        {jrCore_list module="jrVideo" order_by=$order_by template="videos_row.tpl" search="video_category like `$_post.search_string`" pagebreak=$_conf.jrMediaPro_default_pagebreak page=$_post.p}
                                    {elseif !empty($_post._1)}
                                        {if isset($_post.option) && $_post.option == 'by_album'}
                                            {jrCore_list module="jrVideo" order_by=$order_by group_by="video_album_url" template="videos_row.tpl" search="video_album like `$_post._1`%" pagebreak=$_conf.jrMediaPro_default_pagebreak page=$_post.p}
                                        {else}
                                            {jrCore_list module="jrVideo" order_by=$order_by template="videos_row.tpl" search="video_title like `$_post._1`%" pagebreak=$_conf.jrMediaPro_default_pagebreak page=$_post.p}
                                        {/if}
                                    {else}
                                        {if isset($_post.option) && $_post.option == 'by_album'}
                                            {jrCore_list module="jrVideo" order_by=$order_by group_by="video_album_url" template="videos_row.tpl" pagebreak=$_conf.jrMediaPro_default_pagebreak page=$_post.p}
                                        {else}
                                            {jrCore_list module="jrVideo" order_by=$order_by template="videos_row.tpl" pagebreak=$_conf.jrMediaPro_default_pagebreak page=$_post.p}
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
                {jrCore_include template="side_videos.tpl"}
            </div>
        </div>

    </div>
</div>

{jrCore_include template="footer.tpl"}
