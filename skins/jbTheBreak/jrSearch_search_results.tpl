{jrCore_module_url module="jrSearch" assign="murl"}
<div class="container">
    <div class="row">
        <div class="col12 last">
            <div class="title" style="margin-bottom:12px">


                {if $module_count == 1}
                    <h1>&quot;{$search_string}&quot; in {$titles[$modules]}</h1>
                    <div class="breadcrumbs">
                        <a href="{$jamroom_url}">Home</a> &raquo; <a href="{$jamroom_url}/{$murl}/results/all/{$page}/{$pagebreak}/search_string={$search_string}">{jrCore_lang module="jrSearch" id="6" default="All Search Results for"} &quot;{$search_string}&quot;</a> &raquo; {$titles[$modules]}
                    </div>
                    <div style="margin:12px 0 0 0">
                        <form method="post" action="{$jamroom_url}/{$murl}/results/{$modules}/{$page}/{$pagebreak}" target="_self">
                            <input type="text" name="search_string" class="form_text" value="{$search_string}">&nbsp;<input type="submit" class="form_button" value="{jrCore_lang module="jrSearch" id="7" default="search"} {$titles[$modules]}">
                        </form>
                    </div>
                {else}
                    <h1>{jrCore_lang module="jrSearch" id="8" default="Search Results for"} &quot;{$search_string}&quot;</h1>
                    <div style="margin:12px 0 0 0">
                    <form method="post" action="{$jamroom_url}/{$murl}/results/{$modules}/{$page}/{$pagebreak}" target="_self">
                        <input type="text" name="search_string" class="form_text" value="{$search_string}">&nbsp;<input type="submit" class="form_button" value="{jrCore_lang module="jrSearch" id="7" default="search"}">
                    </form>
                    </div>
                {/if}

            </div>
        </div>
    </div>


    {if count($results) > 0}

        {foreach $results as $module => $result}

            <div class="row">
                <div class="col12 last">
                    <div style="margin:12px">
                        <div class="title">
                            {if $module_count > 1}
                            <div class="block_config">
                                <a href="{$jamroom_url}/{$murl}/results/{$module}/{$page}/8/search_string={$search_string}">{jrCore_lang module="jrSearch" id="9" default="See All Results"} ({$info[$module].total_items}) &raquo; </a>
                            </div>
                            {/if}
                            <h1>{$titles[$module]}</h1>
                        </div>

                        <div class="block_content">{$result}</div>

                    </div>
                </div>
            </div>

        {/foreach}

    {else}

        <div class="row">
            <div class="col12 last">
                <div style="margin:12px">
                    <div class="title">
                        {jrCore_lang module="jrSearch" id="10" default="No results found for your search"}
                    </div>
                </div>
            </div>
        </div>

    {/if}

</div>