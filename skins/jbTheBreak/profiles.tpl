{assign var="selected" value="lists"}
{assign var="spt" value="profiles"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="12" default="Profiles" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrSetActive('#default');
         });
</script>

<div id="content">

    <div class="container">

        <div class="row">

            {* BEGIN LEFT SIDE *}
            <div class="col9">
                <div class="body_1 mr5">
                    <div class="container">

                        <div class="row">

                            <div class="col12 last">

                                <h1><span style="font-weight:normal;">{jrCore_lang skin=$_conf.jrCore_active_skin id="22" default="All"}</span>&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="12" default="Profiles"}</h1><br>
                                <br>
                                <div id="profiles" class="mb20">
                                    {if isset($_conf.jbTheBreak_admin_quota) && $_conf.jbTheBreak_admin_quota != $_conf.jbTheBreak_artist_quota}
                                        {jrCore_list module="jrProfile" order_by="profile_name asc" group_by="profile_quota_id" search="profile_quota_id != `$_conf.jbTheBreak_admin_quota`" template="profiles_row.tpl" pagebreak="2" page=$_post.p pager=true}
                                    {else}
                                        {jrCore_list module="jrProfile" order_by="profile_name asc" group_by="profile_quota_id" template="profiles_row.tpl" pagebreak="2" page=$_post.p pager=true}
                                    {/if}
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            {* BEGIN RIGHT SIDE *}
            <div class="col3 last">
                <div class="body_1">
                    {jrCore_include template="side_home.tpl"}
                </div>
            </div>

        </div>

    </div>

    {jrCore_include template="footer.tpl"}

