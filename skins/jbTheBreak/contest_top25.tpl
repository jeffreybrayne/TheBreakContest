<div class="row">

            <div class="col12 last">
                <br>
                <br>
                <br>
                <h1><span style="font-weight: normal;">{jrCore_lang skin=$_conf.jrCore_active_skin id="58" default="top"}</span>&nbsp;25&nbsp;<span style="font-weight: normal;">{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="Artists" contestId="#2#"}</span></h1><br>
                <br>
                <div class="mb30 pt20">
                    <div id="top_artists">
						{if isset($_conf.jbTheBreak_require_images) && $_conf.jbTheBreak_require_images == 'on'}
						    {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc" limit="25" search1="profile_jrAudio_item_count > 0" quota_id={$_params.quota_id} template="index_top_artists_row.tpl" require_image="profile_image"}
						{else}
						    {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc" limit="25" search1="profile_jrAudio_item_count > 0" quota_id={$_params.quota_id} template="index_top_artists_row.tpl"}
						{/if}
                    </div>
                </div>

            </div>

        </div>