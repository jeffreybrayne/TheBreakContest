<!-- Search Song Genre-->
{if isset($_post.option) && $_post.option == "monthly"}
    {assign var="c_days" value="30"}
{elseif isset($_post.option) && $_post.option == "yearly"}
    {assign var="c_days" value="365"}
{else}
    {assign var="c_days" value="7"}
{/if}
<h3>{jrCore_lang module="jrVideo" id="12" default="Category"} {jrCore_lang skin=$_conf.jrCore_active_skin id="24" default="Search"}</h3>
<br />
<form class="margin" method="post" action="{$jamroom_url}/video_charts{if isset($_post.option) && strlen($_post.option) > 0}_{$_post.option}{/if}">
    <input type="hidden" name="search_area" value="video_category">
    <select class="form_select" name="search_string" style="width:100%; font-size:13px;" onchange="this.form.submit()">
        {if isset($_post.search_area) && $_post.search_area == 'video_category'}
            <option value="{$_post.search_string}">{$_post.search_string}</option>
        {else}
            <option value="">{jrCore_lang skin=$_conf.jrCore_active_skin id="184" default="Select A Cateogry"}</option>
        {/if}
        {jrCore_list module="jrVideo" chart_field="video_file_stream_count" chart_days=$c_days group_by="video_category" limit="200" template="video_chart_categories_row.tpl"}
    </select>
</form>
