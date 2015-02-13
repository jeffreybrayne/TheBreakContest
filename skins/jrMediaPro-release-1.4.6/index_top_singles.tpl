{if jrCore_module_is_active('jrRating')}
    {if isset($_conf.jrMediaPro_require_images) && $_conf.jrMediaPro_require_images == 'on'}
        {jrCore_list module="jrAudio" order_by="audio_rating_overall_average_count numerical_desc" quota_id=$_conf.jrMediaPro_artist_quota search1="profile_active = 1" template="index_top_singles_rating_row.tpl" require_image="audio_image" pagebreak="6" page=$_post.p}
    {else}
        {jrCore_list module="jrAudio" order_by="audio_rating_overall_average_count numerical_desc" search1="profile_active = 1" quota_id=$_conf.jrMediaPro_artist_quota template="index_top_singles_rating_row.tpl" pagebreak="6" page=$_post.p}
    {/if}
{else}
    {if isset($_conf.jrMediaPro_require_images) && $_conf.jrMediaPro_require_images == 'on'}
        {jrCore_list module="jrAudio" order_by="audio_file_stream_count numerical_desc" quota_id=$_conf.jrMediaPro_artist_quota search1="profile_active = 1" template="index_top_singles_row.tpl" require_image="audio_image" pagebreak="6" page=$_post.p}
    {else}
        {jrCore_list module="jrAudio" order_by="audio_file_stream_count numerical_desc" search1="profile_active = 1" quota_id=$_conf.jrMediaPro_artist_quota template="index_top_singles_row.tpl" pagebreak="6" page=$_post.p}
    {/if}
{/if}