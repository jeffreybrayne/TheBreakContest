<div class="block_content">
    <div class="item" style="display:table;width:100%;">
        <div style="display:table-row">
            <div style="display:table-cell;width:10%;">
                <img src="{$jamroom_url}/modules/jrSupport/img/modules.png" width="128" alt="help with modules and functionality">
            </div>
            <div style="display:table-cell;padding:0 18px;vertical-align:middle;width:90%">
                <h2>Module Questions</h2>
                <ul>
                    <li>Have a question about how a module works?</li>
                    <li>Need help configuring a module to suit your needs?</li>
                    <li>Encountered an issue with a module and need help?</li>
                </ul>
            {if isset($_mods)}
                <select name="module" class="form_select" onchange="var v=this.options[this.selectedIndex].value; jrSupport_view_options('module', v);">
                <option value=""> -- Select the Module you need help with --</option>
                {foreach $_modules as $mod}
                    <option value="{$mod.module_directory}"> {$mod.module_name}</option>
                {/foreach}
                </select>&nbsp;<img id="module_submit_indicator" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/form_spinner.gif" width="24" height="24" alt="{jrCore_lang module="jrCore" id="73" default="working..."}" style="display:none;margin:2px 0 7px 6px">
            {/if}
            </div>
        </div>
    </div>
</div>

<div id="module_info" style="display:none">{* help results load here *}</div>

<div class="block_content">
    <div class="item" style="display:table;width:100%;">
        <div style="display:table-row">
            <div style="display:table-cell;width:10%;">
                <img src="{$jamroom_url}/modules/jrSupport/img/skins.png" width="128" alt="help with skin customization">
            </div>
            <div style="display:table-cell;padding:0 18px;vertical-align:middle;width:90%">
                <h2>Skin Questions and Customization</h2>
                <ul>
                    <li>Need help designing or customizing the skin templates?</li>
                    <li>Have questions about a skin configuration?</li>
                    <li>Encountered an issue and need help?</li>
                </ul>
                {if isset($_skins)}
                    <select name="skin" class="form_select" onchange="var v=this.options[this.selectedIndex].value; jrSupport_view_options('skin', v);">
                    <option value=""> -- Select the Skin you need help with --</option>
                        {foreach $_skins as $skin => $title}
                            <option value="{$skin}"> {$title}</option>
                        {/foreach}
                    </select>&nbsp;<img id="skin_submit_indicator" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/form_spinner.gif" width="24" height="24" alt="{jrCore_lang module="jrCore" id="73" default="working..."}" style="display:none;margin:2px 0 7px 6px">
                {/if}
            </div>
        </div>
    </div>
</div>

<div id="skin_info">{* help results load here *}</div>
