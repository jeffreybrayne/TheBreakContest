<div class="block_search">
    {if isset($_post.ss)}
        <h3>{jrCore_lang module="jrSearch" id=6 default="All Search Results for"} &quot;{$_post.ss|jrCore_entity_string}&quot;</h3> &nbsp;
        <input type="button" class="form_button" value="{jrCore_lang module="jrSearch" id=12 default="Reset"}" onclick="jrCore_window_location('{$jamroom_url}/{$search_url}');">
    {else}
        {jrCore_module_url module="jrImage" assign="url"}
        {jrCore_lang module="jrSearch" id=7 default="search" assign="sv"}
        <img id="form_submit_indicator" src="{$jamroom_url}/{$url}/img/skin/{$_conf.jrCore_active_skin}/form_spinner.gif" width="24" height="24" alt="{jrCore_lang module="jrCore" id="73" default="working..."}">&nbsp;<input id="search_module" type="text" name="ss" class="form_text form_text_search" value="{$sv|jrCore_entity_string}" onfocus="if(this.value=='{$sv|jrCore_entity_string}'){ this.value=''; }" onblur="if(this.value==''){ this.value='{$sv|jrCore_entity_string}'; }" onkeypress="if (event && event.keyCode == 13) { jrSearch_module_index('{$search_url}','{$fields|jrCore_url_encode_string}'); }">
    {/if}
</div>