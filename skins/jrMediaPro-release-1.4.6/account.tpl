{assign var="page_title" value=$_1}
{assign var="selected" value="lists"}
{assign var="spt" value=$_1}
{assign var="no_inner_div" value="true"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function () {
        jrSetActive('#default');
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

                            {* ALPHABETICALLY *}
                            <a id="alphaaccounts" name="alphaaccounts"></a>
                            <h2>{$page_title}</h2>
                            <div class="body_1 mb20">
                                <div class="br-info capital mb20">
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="66" default="alphabetically"}&nbsp;&raquo;&nbsp;<a onclick="jrLoad('#alpha_accounts','{$jamroom_url}/account_list/qid={$option}/order=alpha');$('html, body').animate({ scrollTop: $('#alphaaccounts').offset().top -100 }, 'slow');return false;">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
                                </div>
                                <div id="alpha_accounts">
                                    {jrCore_include template="account_list.tpl" qid=$option order="alpha"}
                                </div>
                            </div>

                            {* NEWEST *}
                            <a id="newaccounts" name="newaccounts"></a>
                            <div class="body_1 mb20">
                                <div class="br-info capital mb20">
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;&raquo;&nbsp;<a onclick="jrLoad('#new_accounts','{$jamroom_url}/account_list/qid={$option}/order=newest');$('html, body').animate({ scrollTop: $('#newaccounts').offset().top -100 }, 'slow');return false;">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
                                </div>
                                <div id="new_accounts">
                                    {jrCore_include template="account_list.tpl" qid=$option order="newest"}
                                </div>
                            </div>

                            {* MOST VIEWED *}
                            <a id="viewedaccounts" name="viewedaccounts"></a>
                            <div class="body_1 mb20">
                                <div class="br-info capital mb20">
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="46" default="most"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="47" default="viewed"}&nbsp;&raquo;&nbsp;<a onclick="jrLoad('#most_viewed_accounts','{$jamroom_url}/account_list/qid={$option}/order=most_viewed');$('html, body').animate({ scrollTop: $('#viewedaccounts').offset().top -100 }, 'slow');return false;">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
                                </div>
                                <div id="most_viewed_accounts">
                                    {jrCore_include template="account_list.tpl" qid=$option order="most_viewed"}
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
