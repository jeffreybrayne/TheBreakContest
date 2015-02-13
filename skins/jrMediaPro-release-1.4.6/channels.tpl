{assign var="selected" value="channels"}
{assign var="spt" value="channels"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="151" default="Channel Center" assign="page_title"}
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
                            <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="151" default="Channel Center"}</h2>
                            <div class="body_3 mb20">
                                <div id="channel_div">

                                {if isset($_conf.jrMediaPro_auto_play) && $_conf.jrMediaPro_auto_play == 'on'}
                                    {assign var="cap" value="true"}
                                {else}
                                    {assign var="cap" value="false"}
                                {/if}

                                {if isset($_post.option) && strlen($_post.option) > 0}
                                    {jrCore_list module="jrPlaylist" order_by="_created desc" search1="playlist_list like %jrVideo%" search2="playlist_title_url = `$_post.option`" limit="1" template="channels_playlist.tpl" autoplay=$cap}
                                {elseif isset($_conf.jrMediaPro_tv_title) && strlen($_conf.jrMediaPro_tv_title) > 0}
                                    {jrCore_list module="jrPlaylist" order_by="_created desc" search1="playlist_list like %jrVideo%" search2="playlist_title = `$_conf.jrMediaPro_tv_title`" limit="1" template="channels_playlist.tpl" autoplay=$cap}
                                {else}
                                    {jrCore_list module="jrPlaylist" order_by="_created random" search1="playlist_list like %jrVideo%" limit="1" template="channels_playlist.tpl" autoplay=$cap}
                                {/if}

                                </div>
                            </div>
                        </div>
                    </div>

                    {if jrCore_module_is_active('jrRating')}
                    <a id="topchannel" name="topchannel"></a>
                    <div class="row">
                        <div class="col12 last">
                            <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="58" default="top"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="139" default="Channels"}</h2>
                            <div class="body_1 mb20">
                                <div id="top_channel_div">
                                {jrCore_list module="jrPlaylist" order_by="playlist_rating_1_average_count desc" search1="playlist_list like %jrVideo%" template="top_channels_row.tpl" pagebreak="7" page=$_post.p}
                                </div>
                            </div>
                        </div>
                    </div>
                    {/if}

                    <a id="newchannel" name="newchannel"></a>
                    <div class="row">
                        <div class="col12 last">
                            <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="139" default="Channels"}</h2>
                            <div class="body_1">
                                <div id="newest_channel_div">
                                {if jrCore_module_is_active('jrRating')}
                                    {jrCore_list module="jrPlaylist" order_by="_created desc" search1="playlist_list like %jrVideo%" template="channels_row.tpl" pagebreak="7" page=$_post.p}
                                {else}
                                    {jrCore_list module="jrPlaylist" order_by="_created desc" search1="playlist_list like %jrVideo%" template="channels_row.tpl" pagebreak="15" page=$_post.p}
                                {/if}
                                </div>
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
