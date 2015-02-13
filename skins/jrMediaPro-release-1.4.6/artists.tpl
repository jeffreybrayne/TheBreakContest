{assign var="selected" value="lists"}
{assign var="spt" value="artist"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="Artists" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrSetActive('#default');
        jrLoad('#hot_artists',core_system_url + '/hot_artists');
        jrLoad('#artists_newest',core_system_url + '/artists_newest');
        jrLoad('#top_artists',core_system_url + '/index_top_artists');
     });
</script>
{if isset($_post.option) && $_post.option == 'by_newest'}
    {assign var="order_by" value="_created desc"}
{else}
    {assign var="order_by" value="profile_name asc"}
{/if}


<div class="container">
    <div class="row">

        <div class="col9">
            <div class="body_1 mr5">
                <div class="container">

                    {* FEATURED ARTIST *}
                    <div class="row">

                        <div class="col12 last">

                            <h1><span style="font-weight:normal;">{jrCore_lang skin=$_conf.jrCore_active_skin id="21" default="Featured"}</span>&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="Artist"}</h1>
                            <div id="featured_artists" class="mb20">

                                {if isset($_conf.jrMediaPro_profile_ids) && $_conf.jrMediaPro_profile_ids > 0}
                                    {jrCore_list module="jrProfile" order_by="_profile_id numerical_asc" limit="10" search1="profile_active = 1" search2="_profile_id in `$_conf.jrMediaPro_profile_ids`" template="index_featured.tpl" pagebreak="1" page=$_post.p}
                                {else}
                                    {if isset($_conf.jrMediaPro_require_images) && $_conf.jrMediaPro_require_images == 'on'}
                                        {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc" limit="10" quota_id=$_conf.jrMediaPro_artist_quota search1="profile_active = 1" template="index_featured.tpl" require_image="profile_image" pagebreak="1" page=$_post.p}
                                    {else}
                                        {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc" limit="10" quota_id=$_conf.jrMediaPro_artist_quota search1="profile_active = 1" template="index_featured.tpl" pagebreak="1" page=$_post.p}
                                    {/if}
                                {/if}

                            </div>

                        </div>

                    </div>

                    {* HOT ARTISTS *}
                    <a id="hotartists" name="hotartists"></a>
                    <div class="row">

                        <div class="col12 last">

                            <h1><span style="font-weight: normal;">{jrCore_lang skin=$_conf.jrCore_active_skin id="174" default="Hot"}</span> {jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="Artists"}</h1><br>
                            <br>
                            <div class="top_singles_body mb30 pt20">
                                <div id="hot_artists">
                                </div>
                            </div>

                        </div>

                    </div>

                    {* NEWEST ARTISTS *}
                    <div class="row">

                        <div class="col12 last">

                            <h1><span style="font-weight: normal;">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="Newest"}</span> {jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="Artists"}</h1><br>
                            <div class="mb30 pt20">
                                <div id="artists_newest">

                                </div>
                            </div>

                        </div>

                    </div>

                    {* TOP 10 ARTISTS *}
                    <div class="row">

                        <div class="col12 last">

                            <h1><span style="font-weight: normal; color:#FF3399;">{jrCore_lang skin=$_conf.jrCore_active_skin id="58" default="top"}</span>&nbsp;10&nbsp;<span style="font-weight: normal; color:#FF3399;">{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="Artists"}</span></h1><br>
                            <br>
                            <div class="mb30 pt20">
                                <div id="top_artists">

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
