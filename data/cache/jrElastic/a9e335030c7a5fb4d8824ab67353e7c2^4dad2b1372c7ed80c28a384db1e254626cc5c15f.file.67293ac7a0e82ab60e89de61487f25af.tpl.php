<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:25:15
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/67293ac7a0e82ab60e89de61487f25af.tpl" */ ?>
<?php /*%%SmartyHeaderCode:118847235354d674ab2c7b25-50232865%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4dad2b1372c7ed80c28a384db1e254626cc5c15f' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/67293ac7a0e82ab60e89de61487f25af.tpl',
      1 => 1423340715,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '118847235354d674ab2c7b25-50232865',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'jamroom_url' => 0,
    '_mods' => 0,
    '_modules' => 0,
    'mod' => 0,
    '_conf' => 0,
    '_skins' => 0,
    'skin' => 0,
    'title' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d674ab3ca423_92401830',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d674ab3ca423_92401830')) {function content_54d674ab3ca423_92401830($_smarty_tpl) {?><div class="block_content">
    <div class="item" style="display:table;width:100%;">
        <div style="display:table-row">
            <div style="display:table-cell;width:10%;">
                <img src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/modules/jrSupport/img/modules.png" width="128" alt="help with modules and functionality">
            </div>
            <div style="display:table-cell;padding:0 18px;vertical-align:middle;width:90%">
                <h2>Module Questions</h2>
                <ul>
                    <li>Have a question about how a module works?</li>
                    <li>Need help configuring a module to suit your needs?</li>
                    <li>Encountered an issue with a module and need help?</li>
                </ul>
            <?php if (isset($_smarty_tpl->tpl_vars['_mods']->value)) {?>
                <select name="module" class="form_select" onchange="var v=this.options[this.selectedIndex].value; jrSupport_view_options('module', v);">
                <option value=""> -- Select the Module you need help with --</option>
                <?php  $_smarty_tpl->tpl_vars['mod'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['mod']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['_modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['mod']->key => $_smarty_tpl->tpl_vars['mod']->value) {
$_smarty_tpl->tpl_vars['mod']->_loop = true;
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['mod']->value['module_directory'];?>
"> <?php echo $_smarty_tpl->tpl_vars['mod']->value['module_name'];?>
</option>
                <?php } ?>
                </select>&nbsp;<img id="module_submit_indicator" src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/skins/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
/img/form_spinner.gif" width="24" height="24" alt="<?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrCore",'id'=>"73",'default'=>"working..."),$_smarty_tpl); } ?>
" style="display:none;margin:2px 0 7px 6px">
            <?php }?>
            </div>
        </div>
    </div>
</div>

<div id="module_info" style="display:none"></div>

<div class="block_content">
    <div class="item" style="display:table;width:100%;">
        <div style="display:table-row">
            <div style="display:table-cell;width:10%;">
                <img src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/modules/jrSupport/img/skins.png" width="128" alt="help with skin customization">
            </div>
            <div style="display:table-cell;padding:0 18px;vertical-align:middle;width:90%">
                <h2>Skin Questions and Customization</h2>
                <ul>
                    <li>Need help designing or customizing the skin templates?</li>
                    <li>Have questions about a skin configuration?</li>
                    <li>Encountered an issue and need help?</li>
                </ul>
                <?php if (isset($_smarty_tpl->tpl_vars['_skins']->value)) {?>
                    <select name="skin" class="form_select" onchange="var v=this.options[this.selectedIndex].value; jrSupport_view_options('skin', v);">
                    <option value=""> -- Select the Skin you need help with --</option>
                        <?php  $_smarty_tpl->tpl_vars['title'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['title']->_loop = false;
 $_smarty_tpl->tpl_vars['skin'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['_skins']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['title']->key => $_smarty_tpl->tpl_vars['title']->value) {
$_smarty_tpl->tpl_vars['title']->_loop = true;
 $_smarty_tpl->tpl_vars['skin']->value = $_smarty_tpl->tpl_vars['title']->key;
?>
                            <option value="<?php echo $_smarty_tpl->tpl_vars['skin']->value;?>
"> <?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</option>
                        <?php } ?>
                    </select>&nbsp;<img id="skin_submit_indicator" src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/skins/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
/img/form_spinner.gif" width="24" height="24" alt="<?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrCore",'id'=>"73",'default'=>"working..."),$_smarty_tpl); } ?>
" style="display:none;margin:2px 0 7px 6px">
                <?php }?>
            </div>
        </div>
    </div>
</div>

<div id="skin_info"></div>
<?php }} ?>
