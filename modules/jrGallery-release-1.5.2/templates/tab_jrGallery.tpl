{* for the jrEmbed module to add a tab to the tinymce popup *}
{jrCore_module_url module="jrGallery" assign="murl"}
<script type="text/javascript">
function jrGallery_load_images(p, profile_url, ss) {
    $('#gallery_form_submit_indicator').show(300, function () {
        $('#jrGallery_holder').load(core_system_url + '/{$murl}/tab/p=' + p + '/profile_url=' + jrE(profile_url) + '/ss=' + jrE(ss) + '/__ajax=1', function () {
            $('#gallery_form_submit_indicator').hide(300);
            $('#gallery_sstr').val("");
        });
    });
}
$(document).ready(function(){
    jrGallery_load_images(1);
});
</script>


<table class="page_table">
    <tbody>
    <tr>
        <td class="element_left search_area_left">
            {jrCore_lang module="jrCore" id="8" default="Search"}
            <img id="gallery_form_submit_indicator" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/form_spinner.gif" width="24" height="24" alt="working..."></td>
        <td class="element_right search_area_right">
            <div id="gallery_search_options">
                <input type="text" onkeypress="if (event &amp;&amp; event.keyCode == 13 &amp;&amp; this.value.length &gt; 0) { var s=$('#gallery_sstr').val();jrGallery_load_images(1,false, jrE(s));return false; }" value="" class="form_text form_text_search" id="gallery_sstr" name="search_string">
                <input type="button" onclick="var s=$('#gallery_sstr').val();jrGallery_load_images(1,false, jrE(s));return false;" class="form_button" value="{jrCore_lang module="jrCore" id="8" default="Search"}">
                <input type="button" onclick="jrGallery_load_images(1);" class="form_button" value="{jrCore_lang module="jrCore" id="29" default="reset"}">
            </div>
        </td>
    </tr>
    </tbody>
</table>


<div id="jrGallery_holder">
    {jrCore_lang module="jrCore" id="73" default="working..." assign="alt"}
    <div style="padding:12px;">
        {jrCore_image module="jrEmbed" image="spinner.gif" width="24" height="24" alt=$alt}
    </div>
</div>

