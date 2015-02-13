<!-- Search Song Genre-->
<h3>{jrCore_lang module="jrAudio" id="12" default="Genre"} {jrCore_lang skin=$_conf.jrCore_active_skin id="24" default="Search"}</h3>
<br />
<form class="margin" method="post" action="{$jamroom_url}/music">
    <input type="hidden" name="search_area" value="audio_genre">
    <select class="form_select" name="search_string" style="width:100%; font-size:13px;" onchange="this.form.submit()">
        {if isset($_post.search_area) && $_post.search_area == 'audio_genre'}
            <option value="{$_post.search_string}">{$_post.search_string}</option>
        {else}
            <option value="">{jrCore_lang skin=$_conf.jrCore_active_skin id="183" default="Select A Genre"}</option>
        {/if}
        {jrCore_list module="jrAudio" order_by="audio_genre asc" group_by="audio_genre" limit="200" template="music_genres_row.tpl"}
    </select>
</form>
