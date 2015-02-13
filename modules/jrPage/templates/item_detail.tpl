{jrCore_module_url module="jrPage" assign="murl"}

<div class="block">

{if $item.page_location == 1}

    {if !isset($item.page_header) || $item.page_header == 'on'}
    <div class="title">
        <div class="block_config">
            {jrCore_item_detail_buttons module="jrPage" item=$item}
        </div>
        <h1>{$item.page_title}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a> &raquo; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jrPage" id="19" default="Pages"}</a> &raquo; {$item.page_title}
        </div>
    </div>
    {/if}

{else}

    {if !isset($item.page_header) || $item.page_header == 'on'}
    <div class="title">
        <h1>{$item.page_title}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jrPage" id="19" default="Pages"}</a> &raquo; {$item.page_title}
        </div>
    </div>
    {/if}

{/if}


    <div id="pc{$item._item_id}" class="block_content">

        {if !isset($item.page_header) || $item.page_header == 'on'}
        <div class="item jrpage_body">
        {else}
        <div class="item jrpage_body" style="position:relative;margin-top:0">
        {/if}

            {if jrUser_can_edit_item($item) && isset($item.page_header) && $item.page_header == 'off'}
            <div id="pm{$item._item_id}" class="page_actions">
                {jrCore_item_detail_buttons module="jrPage" item=$item}
            </div>
            <script>$(function () { $('#pc{$item._item_id}').hover(function() { $('#pm{$item._item_id}').toggle(); }); });</script>
            {/if}

            {$item.page_body|jrCore_format_string:$item.profile_quota_id:null:nl2br}

        </div>

        {* bring in module features if enabled *}
        {if !isset($item.page_features) || $item.page_features == 'on'}
            {jrCore_item_detail_features module="jrPage" item=$item}
        {/if}

    </div>

</div>
