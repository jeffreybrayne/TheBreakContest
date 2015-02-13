<!-- Search Song Genre-->
<h3>{jrCore_lang module="jrVideo" id="12" default="Category"} {jrCore_lang skin=$_conf.jrCore_active_skin id="24" default="Search"}</h3>
<br />
<form class="margin" method="post" action="{$jamroom_url}/videos">
    <input type="hidden" name="search_area" value="video_category">
    <select class="form_select" name="search_string" style="width:100%; font-size:13px;" onchange="this.form.submit()">
        {if isset($_post.search_area) && $_post.search_area == 'video_category'}
            <option value="{$_post.search_string}">{$_post.search_string}</option>
        {else}
            <option value="">{jrCore_lang skin=$_conf.jrCore_active_skin id="184" default="Select A Cateogry"}</option>
        {/if}
        {jrCore_list module="jrVideo" order_by="video_category asc" group_by="video_category" limit="200" template="video_categories_row.tpl"}
    </select>
</form>
