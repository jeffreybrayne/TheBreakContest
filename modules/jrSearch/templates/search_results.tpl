{jrCore_module_url module="jrSearch" assign="murl"}
<div class="container search_results_container">
    <div class="row">
        <div class="col12 last">
            <div class="title" style="margin-bottom:12px">

                {if $module_count == 1}
                    <h1>&quot;{$search_string}&quot; in {$titles[$modules]}</h1>
                    <div class="breadcrumbs">
                        <a href="{$jamroom_url}">{jrCore_lang module="jrSearch" id="11" default="Home"}</a> &raquo; <a href="{$jamroom_url}/{$murl}/results/all/1/4/search_string={$search_string}">{jrCore_lang module="jrSearch" id="6" default="All Search Results for"} &quot;{$search_string}&quot;</a> &raquo; {$titles[$modules]}
                    </div>
                {else}
                    <h1>{jrCore_lang module="jrSearch" id="8" default="Search Results for"} &quot;{$search_string}&quot;</h1>
                {/if}

                <div style="margin:12px 0 0 0">
                    <form method="get" action="{$jamroom_url}/{$murl}/results/{$modules}/{$page}/{$pagebreak}" target="_self" onsubmit="$('#form_submit_indicator').show(300, function() { return true } );">
                        <input type="text" name="search_string" class="form_text" value="{$search_string}">
                        <br><span style="display:inline-block;margin-top:8px;"><img id="form_submit_indicator" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/submit.gif" width="24" height="24" alt="{jrCore_lang module="jrCore" id="73" default="working..."}"><input type="submit" class="form_button" value="{jrCore_lang module="jrSearch" id="7" default="search"} {$titles[$modules]}"></span>
                    </form>
                </div>

            </div>
        </div>
    </div>


    {if count($results) > 0}

        {foreach $results as $module => $result}

        {if $module_count > 1}
            {if $result@iteration % 2 === 1}
                <div class="row">
                    <div class="col6">
                        <div style="margin:6px 6px 12px 0">
            {else}
                    <div class="col6 last">
                        <div style="margin:6px 0 6px 6px">
            {/if}
        {else}
            <div class="row">
                <div class="col12 last">
                    <div style="margin:12px 0">
        {/if}
                        <div class="title">
                            {if $module_count > 1}
                            <div class="block_config">
                                {if $info[$module].total_items > $pagebreak}
                                {if $module == 'jrGallery'}
                                    <a href="{$jamroom_url}/{$murl}/results/{$module}/{$page}/12/search_string={$search_string}">{jrCore_lang module="jrSearch" id="9" default="See All Results"} ({$info[$module].total_items}) &raquo; </a>
                                {else}
                                    <a href="{$jamroom_url}/{$murl}/results/{$module}/{$page}/{$pagebreak}/search_string={$search_string}">{jrCore_lang module="jrSearch" id="9" default="See All Results"} ({$info[$module].total_items}) &raquo; </a>
                                {/if}
                                {/if}
                            </div>
                            {/if}
                            <h1>{$titles[$module]}</h1>
                        </div>

                        <div class="block_content">{$result}</div>

                    </div>
                </div>

            {if $result@iteration % 2 === 0 || $module_count == '1' || $result@last === true}
            </div>
            {/if}

        {/foreach}

        {* prev/next page profile footer links *}
        {if $module_count == 1}
        {if $info[$module].prev_page > 0 || $info[$module].next_page > 0}
            <div class="block">
                <table style="width:100%">
                    <tr>
                        <td style="width:25%">
                        {if $info[$module].prev_page > 0}
                            <input type="button" class="form_button" value="&lt;" onclick="window.location='{$jamroom_url}/{$murl}/results/{$module}/{$info[$module].prev_page}/{$pagebreak}/search_string={$search_string}'">
                        {/if}
                        </td>
                        <td style="width:50%;text-align:center">
                            {$info[$module].this_page}&nbsp;/&nbsp;{$info[$module].total_pages}
                        </td>
                        <td style="width:25%;text-align:right">
                        {if $info[$module].next_page > 0}
                            <input type="button" class="form_button" value="&gt;" onclick="window.location='{$jamroom_url}/{$murl}/results/{$module}/{$info[$module].next_page}/{$pagebreak}/search_string={$search_string}'">
                        {/if}
                        </td>
                    </tr>
                </table>
            </div>
        {/if}
        {/if}

    {else}

        <div class="row">
            <div class="col12 last">
                <div class="title" style="margin-bottom:12px">
                    {jrCore_lang module="jrSearch" id="10" default="No results found for your search"}
                </div>
            </div>
        </div>

    {/if}

</div>

