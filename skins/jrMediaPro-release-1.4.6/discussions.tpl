{assign var="selected" value="discussions"}
{assign var="spt" value="discussions"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="196" default="Discussions" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrSetActive('#default');
        jrLoad('#alpha_discussions',core_system_url + '/discussion_list/alpha');
        jrLoad('#new_discussions',core_system_url + '/discussion_list/newest');
        jrLoad('#top_discussions',core_system_url + '/discussion_list/top');
    });
</script>

<div class="container">
    <div class="row">

        <div class="col9">

            <div class="body_1 mr5">
                <div class="container">

                    <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="196" default="discussions"}</h2>

                    {* TOP DISCUSSIONS *}
                    <a id="topdiscussion" name="topdiscussion"></a>
                    <div class="row">
                        <div class="col12 last">
                            <div class="body_1 mb20">
                                <div class="br-info capital" style="margin-bottom:20px;">
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="58" default="Top"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="196" default="discussions"}&nbsp;&raquo;&nbsp;<a onclick="jrLoad('#top_discussions','{$jamroom_url}/discussion_list/top');$('html, body').animate({ scrollTop: $('#topdiscussion').offset().top });return false;">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
                                </div>
                                <div id="top_discussions">
                                </div>
                            </div>
                        </div>
                    </div>

                    {* NEWEST DISCUSSIONS *}
                    <a id="newdiscussion" name="newdiscussion"></a>
                    <div class="row">
                        <div class="col12 last">
                            <div class="body_1 mb20">
                                <div class="br-info capital" style="margin-bottom:20px;">
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="196" default="discussions"}&nbsp;&raquo;&nbsp;<a onclick="jrLoad('#new_discussions','{$jamroom_url}/discussion_list/newest');$('html, body').animate({ scrollTop: $('#newdiscussion').offset().top });return false;">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
                                </div>
                                <div id="new_discussions">
                                </div>
                            </div>
                        </div>
                    </div>

                    {* DISCUSSIONS ALPHABETICALLY *}
                    <a id="alphadiscussion" name="alphadiscussion"></a>
                    <div class="row">
                        <div class="col12 last">
                            <div class="body_1 mb20">
                                <div class="br-info capital" style="margin-bottom:20px;">
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="196" default="discussions"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="66" default="alphabetically"}&nbsp;&raquo;&nbsp;<a onclick="jrLoad('#alpha_discussions','{$jamroom_url}/discussion_list/alpha');$('html, body').animate({ scrollTop: $('#alphadiscussion').offset().top });return false;">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
                                </div>
                                <div id="alpha_discussions">
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
