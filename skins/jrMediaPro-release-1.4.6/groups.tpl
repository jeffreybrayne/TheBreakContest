{assign var="selected" value="groups"}
{assign var="spt" value="groups"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="195" default="Groups" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrSetActive('#default');
        jrLoad('#alpha_groups',core_system_url + '/group_list/alpha');
        jrLoad('#new_groups',core_system_url + '/group_list/newest');
        jrLoad('#top_groups',core_system_url + '/group_list/top');
    });
</script>

<div class="container">
    <div class="row">

        <div class="col9">

            <div class="body_1 mr5">
                <div class="container">

                    <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="195" default="groups"}</h2>

                    {* TOP GROUPS *}
                    <a id="topgroup" name="topgroup"></a>
                    <div class="row">
                        <div class="col12 last">
                            <div class="body_1 mb20">
                                <div class="br-info capital" style="margin-bottom:20px;">
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="58" default="Top"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="195" default="groups"}&nbsp;&raquo;&nbsp;<a onclick="jrLoad('#top_groups','{$jamroom_url}/group_list/top');$('html, body').animate({ scrollTop: $('#topgroup').offset().top });return false;">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
                                </div>
                                <div id="top_groups">
                                </div>
                            </div>
                        </div>
                    </div>

                    {* NEWEST GROUPS *}
                    <a id="newgroup" name="newgroup"></a>
                    <div class="row">
                        <div class="col12 last">
                            <div class="body_1 mb20">
                                <div class="br-info capital" style="margin-bottom:20px;">
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="195" default="groups"}&nbsp;&raquo;&nbsp;<a onclick="jrLoad('#new_groups','{$jamroom_url}/group_list/newest');$('html, body').animate({ scrollTop: $('#newgroup').offset().top });return false;">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
                                </div>
                                <div id="new_groups">
                                </div>
                            </div>
                        </div>
                    </div>

                    {* GROUPS ALPHABETICALLY *}
                    <a id="alphagroup" name="alphagroup"></a>
                    <div class="row">
                        <div class="col12 last">
                            <div class="body_1 mb20">
                                <div class="br-info capital" style="margin-bottom:20px;">
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="195" default="groups"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="66" default="alphabetically"}&nbsp;&raquo;&nbsp;<a onclick="jrLoad('#alpha_groups','{$jamroom_url}/group_list/alpha');$('html, body').animate({ scrollTop: $('#alphagroup').offset().top });return false;">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
                                </div>
                                <div id="alpha_groups">
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
