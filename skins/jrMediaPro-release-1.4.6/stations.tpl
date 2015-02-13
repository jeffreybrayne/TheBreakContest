{assign var="selected" value="stations"}
{assign var="spt" value="stations"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="146" default="Station Center" assign="page_title"}
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
                            <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="146" default="Station Center"}</h2>
                            <div class="body_3 mb20">
                                <div id="station_div">
                                {if isset($_conf.jrMediaPro_auto_play) && $_conf.jrMediaPro_auto_play == 'on'}
                                    {assign var="sap" value="true"}
                                {else}
                                    {assign var="sap" value="true"}
                                {/if}

                                {if isset($_post.option) && strlen($_post.option) > 0}
                                    {jrCore_list module="jrPlaylist" order_by="_created desc" search1="playlist_list like %jrAudio%" search2="playlist_title_url = `$_post.option`" limit="1" template="station_playlist.tpl" autoplay=$sap}
                                {elseif isset($_conf.jrMediaPro_radio_title) && strlen($_conf.jrMediaPro_radio_title) > 0}
                                    {jrCore_list module="jrPlaylist" order_by="_created desc" search1="playlist_list like %jrAudio%" search2="playlist_title = `$_conf.jrMediaPro_radio_title`" limit="1" template="station_playlist.tpl" autoplay=$sap}
                                {else}
                                    {jrCore_list module="jrPlaylist" order_by="_created random" search1="playlist_list like %jrAudio%" limit="1" template="station_playlist.tpl" autoplay=$sap}
                                {/if}

                                </div>
                            </div>
                        </div>
                    </div>

                    {if jrCore_module_is_active('jrRating')}
                    <a id="topstation" name="topstation"></a>
                    <div class="row">
                        <div class="col12 last">
                            <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="58" default="top"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="138" default="Stations"}</h2>
                            <div class="body_1 mb20">
                                <div id="top_station_div">
                                    {jrCore_list module="jrPlaylist" order_by="playlist_rating_1_average_count desc" search1="playlist_list like %jrAudio%" template="top_stations_row.tpl" pagebreak="7" page=$_post.p}
                                </div>
                            </div>
                        </div>
                    </div>
                    {/if}

                    <a id="newstation" name="newstation"></a>
                    <div class="row">
                        <div class="col12 last">
                            <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="138" default="Stations"}</h2>
                            <div class="body_1">
                                <div id="newest_station_div">
                                {if jrCore_module_is_active('jrRating')}
                                    {jrCore_list module="jrPlaylist" order_by="_created desc" search1="playlist_list like %jrAudio%" template="stations_row.tpl" pagebreak="7" page=$_post.p}
                                {else}
                                    {jrCore_list module="jrPlaylist" order_by="_created desc" search1="playlist_list like %jrAudio%" template="stations_row.tpl" pagebreak="15" page=$_post.p}
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
                {jrCore_include template="side_home.tpl"}
            </div>
        </div>

    </div>
</div>

{jrCore_include template="footer.tpl"}
