The following Marketplace Updates are available for your system:

{if count($module) > 0}
modules:
    {foreach $module as $mod => $_inf}
    {$_inf.module_name}
    {/foreach}
{/if}
{if count($skin) > 0}
skins:
    {foreach $skin as $dir => $_inf}
    {$_inf.title}
    {/foreach}
{/if}

You can install these new updates from your Marketplace:

{$jamroom_url}/{jrCore_module_url module="jrMarket"}/system_update
