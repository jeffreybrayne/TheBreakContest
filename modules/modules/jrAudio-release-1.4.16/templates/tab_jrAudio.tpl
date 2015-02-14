{* for the jrEmbed module to add a tab to the tinymce popup *}
{jrCore_module_url module="jrAudio" assign="murl"}
<script type="text/javascript">
function jrAudio_load_audio(p, profile_url, genre_url, album_url, ss) {
    $('#audio_form_submit_indicator').show(300, function () {
        $('#jrAudio_holder').load(core_system_url + '/{$murl}/tab/p=' + p + '/profile_url=' + jrE(profile_url) + '/genre_url=' + jrE(genre_url) + '/album_url=' + jrE(album_url) + '/ss=' + jrE(ss) + '/__ajax=1', function() {
            $('#audio_form_submit_indicator').hide(300);
            $('#audio_sstr').val('');
        });
    });
}
$(document).ready(function () {
    jrAudio_load_audio(1);
});
</script>

<table class="page_table">
    <tbody>
    <tr>
        <td class="element_left search_area_left">
            {jrCore_lang module="jrCore" id="8" default="Search"}
            <img id="audio_form_submit_indicator" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/form_spinner.gif" width="24" height="24" alt="working..."></td>
        <td class="element_right search_area_right">
            <div id="audio_search_options">
                <input type="text" onkeypress="if (event &amp;&amp; event.keyCode == 13 &amp;&amp; this.value.length &gt; 0) { var s=$('#audio_sstr').val();jrAudio_load_audio(1,false, false, false, jrE(s));return false; }" value="" class="form_text form_text_search" id="audio_sstr" name="search_string">
                <input type="button" onclick="var s=$('#audio_sstr').val();jrAudio_load_audio(1,false, false, false, jrE(s));return false;" class="form_button" value="{jrCore_lang module="jrCore" id="8" default="Search"}">
                <input type="button" onclick="jrAudio_load_audio(1);" class="form_button" value="{jrCore_lang module="jrCore" id="29" default="Reset"}">
            </div>
        </td>
    </tr>
    </tbody>
</table>

<div id="jrAudio_holder">
    {jrCore_lang module="jrCore" id="73" default="working..." assign="alt"}
    <div class="p20">{jrCore_image module="jrEmbed" image="spinner.gif" width="24" height="24" alt=$alt}</div>
</div>