{jrCore_module_url module="jrAudio" assign="murl"}
<div class="block">

    <div class="title">
        <div class="block_config">

            {jrCore_item_index_buttons module="jrAudio" profile_id=$_profile_id}

        </div>
        <h1>{if isset($_post._1) && strlen($_post._1) >0}{$_post._1}{else}{jrCore_lang module="jrAudio" id="41" default="Audio"}{/if}</h1><br>
        <div class="breadcrumbs"><a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrAudio" id="41" default="Audio"}</a> </div>
    </div>

    <div class="block_content">

        {jrCore_list module="jrAudio" profile_id=$_profile_id order_by="audio_display_order numerical_asc" pagebreak="6" page=$_post.p pager=true}

    </div>

</div>
