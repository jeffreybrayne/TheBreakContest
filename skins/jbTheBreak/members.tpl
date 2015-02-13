{assign var="selected" value="lists"}
{assign var="spt" value="member"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="40" default="Members" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrSetActive('#default');
        jrLoad('#alpha_members',core_system_url + '/members_list/alpha');
        jrLoad('#new_members',core_system_url + '/members_list/newest');
        jrLoad('#most_viewed_members',core_system_url + '/members_list/most_viewed');
     });
</script>
{if isset($_post.list) && $_post.list == 'new'}
    {assign var="order_by" value="_created desc"}
{else}
    {assign var="order_by" value="profile_name asc"}
{/if}


<div class="container">
    <div class="row">

        <div class="col9">

            <div class="body_1 mr5">

                <div class="container">
                    <div class="row">
                        <div class="col12 last">

                            {* MEMBERS ALPHABETICALLY *}
                            <a id="alphamembers" name="alphamembers"></a>
                            <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="40" default="members"}</h2>
                            <div class="body_1 mb20">
                                <div class="br-info capital mb20">
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="66" default="alphabetically"}&nbsp;&raquo;&nbsp;<a onclick="jrLoad('#alpha_members','{$jamroom_url}/members_list/alpha');$('html, body').animate({ scrollTop: $('#alphamembers').offset().top });return false;">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
                                </div>
                                <div id="alpha_members">
                                </div>
                            </div>

                            {* NEWEST MEMBERS *}
                            <a id="newmembers" name="newmembers"></a>
                            <div class="body_1 mb20">
                                <div class="br-info capital mb20">
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;&raquo;&nbsp;<a onclick="jrLoad('#new_members','{$jamroom_url}/members_list/newest');$('html, body').animate({ scrollTop: $('#newmembers').offset().top });return false;">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
                                </div>
                                <div id="new_members">
                                </div>
                            </div>

                            {* MOST VIEWED MEMBERS *}
                            <a id="viewedmembers" name="viewedmembers"></a>
                            <div class="body_1 mb20">
                                <div class="br-info capital mb20">
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="46" default="most"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="47" default="viewed"}&nbsp;&raquo;&nbsp;<a onclick="jrLoad('#most_viewed_members','{$jamroom_url}/members_list/most_viewed');$('html, body').animate({ scrollTop: $('#viewedmembers').offset().top });return false;">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
                                </div>
                                <div id="most_viewed_members">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>

        <div class="col3 last">
            <div class="body_1">
                {jrCore_include template="side_members.tpl"}
            </div>
        </div>

    </div>
</div>

{jrCore_include template="footer.tpl"}
