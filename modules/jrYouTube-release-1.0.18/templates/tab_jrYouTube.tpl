{* for the jrEmbed module to add a tab to the tinymce popup *}
{jrCore_module_url module="jrYouTube" assign="murl"}
<script type="text/javascript">
function jrYouTube_load_videos(p, profile_url, category_url, ss) {
    $('#youtube_form_submit_indicator').show(300, function () {
        $('#jrYouTube_holder').load(core_system_url + '/{$murl}/tab/p=' + p + '/profile_url=' + jrE(profile_url) + '/category_url=' + jrE(category_url) + '/ss=' + jrE(ss) + '/__ajax=1', function () {
            $('#youtube_form_submit_indicator').hide(300);
            $('#youtube_sstr').val("");
        });
    });
}
$(document).ready(function () {
    jrYouTube_load_videos(1);
});

/**
 * insert a new id that is not in the datastore.
 */
function jrYouTube_insert_url() {
    var youtube_id = 'not_valid';
    $('#notice_box').fadeOut();
    $.post(core_system_url + '/{$murl}/validate_id/__ajax=1', {
                youtube_url: $('#youtube_url').val()
            },
            function (data) {
                if (data.success) {
                    youtube_id = data.yid;
                    //clean the url down to just the youtube ID.
                    if (youtube_id == false) {
                        $('#youtube_notice_box').hide(300, function () {
                            $('#youtube_notice_box').text('{jrCore_lang module="jrYouTube" id="8" default="Unable to extract the YouTube ID from the URL - please try again or enter the ID"}').addClass('error').fadeIn(300);
                        });
                        return false;
                    }
                    //success, insert.
                    top.tinymce.activeEditor.insertContent('[jrEmbed module="jrYouTube" youtube_id="' + youtube_id + '"]');
                    top.tinymce.activeEditor.windowManager.close();
                    return true;
                } else {
                    $('#notice_box').text('{jrCore_lang module="jrYouTube" id="8" default="Unable to extract the YouTube ID from the URL - please try again or enter the ID"}').addClass('error').fadeIn();
                    return false;
                }
            });

}
</script>


<table class="page_table">
    <tbody>
    <tr>
        <td class="element_left search_area_left">
            {jrCore_lang module="jrCore" id="8" default="Search"}
            <img id="youtube_form_submit_indicator" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/form_spinner.gif" width="24" height="24" alt="working..."></td>
        <td class="element_right search_area_right">
            <div id="youtube_search_options">
                <input type="text" onkeypress="if (event &amp;&amp; event.keyCode == 13 &amp;&amp; this.value.length &gt; 0) { var s=$('#youtube_sstr').val();jrYouTube_load_videos(1,false, false, jrE(s));return false; }" value="" class="form_text form_text_search" id="youtube_sstr" name="search_string">
                <input type="button" onclick="var s=$('#youtube_sstr').val();jrYouTube_load_videos(1,false, false, jrE(s));return false;" class="form_button" value="{jrCore_lang module="jrCore" id="8" default="Search"}">
                <input type="button" onclick="jrYouTube_load_videos(1);" class="form_button" value="{jrCore_lang module="jrCore" id="29" default="reset"}">
            </div>
        </td>
    </tr>
    </tbody>
</table>


<div id="jrYouTube_holder">
    {jrCore_lang module="jrCore" id="73" default="working..." assign="alt"}
    <div style="padding:12px;">
        {jrCore_image module="jrEmbed" image="spinner.gif" width="24" height="24" alt=$alt}
    </div>
</div>