<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:32:01
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/7c514c9f8fc7c5f207471c2b5a319f28.tpl" */ ?>
<?php /*%%SmartyHeaderCode:49881944454d6764144b236-73508698%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cf626311833f164c7829e74a2b621197a186d5c8' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/7c514c9f8fc7c5f207471c2b5a319f28.tpl',
      1 => 1423341121,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '49881944454d6764144b236-73508698',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'active_tab' => 0,
    'jamroom_url' => 0,
    'core_url' => 0,
    '_modules' => 0,
    'curl' => 0,
    'category' => 0,
    '_mods' => 0,
    'mod_dir' => 0,
    '_mod' => 0,
    'url' => 0,
    '_post' => 0,
    '_conf' => 0,
    '_skins' => 0,
    'skin_dir' => 0,
    '_skin' => 0,
    'admin_page_content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d676416351e2_01855302',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d676416351e2_01855302')) {function content_54d676416351e2_01855302($_smarty_tpl) {?><?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrCore",'assign'=>"core_url"),$_smarty_tpl); } ?>


<div id="admin_container" class="container">
    <div class="row">

        <div class="col3">
            <div class="item-list">
                <table>
                    <tr>
                        <td class="page_tab_bar_holder">
                            <ul class="page_tab_bar">
                                <?php if (isset($_smarty_tpl->tpl_vars['active_tab']->value)&&$_smarty_tpl->tpl_vars['active_tab']->value=='skins') {?>
                                    <li id="mtab" class="page_tab page_tab_first">
                                        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/admin">modules</a></li>
                                    <li id="stab" class="page_tab page_tab_last page_tab_active">
                                        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_admin">skins</a></li>
                                <?php } else { ?>
                                    <li id="mtab" class="page_tab page_tab_first page_tab_active">
                                        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/admin">modules</a></li>
                                    <li id="stab" class="page_tab page_tab_last">
                                        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_admin">skins</a></li>
                                <?php }?>
                            </ul>
                        </td>
                    </tr>
                </table>
                <div id="item-holder">


                    <?php if (isset($_smarty_tpl->tpl_vars['_modules']->value)) {?>

                        
                        <dl class="accordion">

                            <dt class="page_section_header admin_section_header">
                            <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrCore",'assign'=>"curl"),$_smarty_tpl); } ?>

                            <input type="text" value="search" name="ss" class="form_text form_admin_search" style="width:90%" onfocus="if ($(this).val() == 'search') { $(this).val(''); }" onblur="if ($(this).val() == '') { $(this).val('search'); }" onkeypress="if (event && event.keyCode == 13 && this.value.length > 0) { jrCore_window_location('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['curl']->value;?>
/search/ss='+ jrE(this.value));return false; };">
                            </dt>

                            <?php  $_smarty_tpl->tpl_vars["_mods"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["_mods"]->_loop = false;
 $_smarty_tpl->tpl_vars["category"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['_modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["_mods"]->key => $_smarty_tpl->tpl_vars["_mods"]->value) {
$_smarty_tpl->tpl_vars["_mods"]->_loop = true;
 $_smarty_tpl->tpl_vars["category"]->value = $_smarty_tpl->tpl_vars["_mods"]->key;
?>
                                <a href=""><dt class="page_section_header admin_section_header"><?php echo $_smarty_tpl->tpl_vars['category']->value;?>
</dt></a>
                                <dd id="c<?php echo $_smarty_tpl->tpl_vars['category']->value;?>
">

                                    
                                    <?php  $_smarty_tpl->tpl_vars["_mod"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["_mod"]->_loop = false;
 $_smarty_tpl->tpl_vars["mod_dir"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['_mods']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["_mod"]->key => $_smarty_tpl->tpl_vars["_mod"]->value) {
$_smarty_tpl->tpl_vars["_mod"]->_loop = true;
 $_smarty_tpl->tpl_vars["mod_dir"]->value = $_smarty_tpl->tpl_vars["_mod"]->key;
?>
                                        <?php if (function_exists('smarty_function_jrCore_get_module_index')) { echo smarty_function_jrCore_get_module_index(array('module'=>$_smarty_tpl->tpl_vars['mod_dir']->value,'assign'=>"url"),$_smarty_tpl); } ?>

                                        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['_mod']->value['module_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['url']->value;?>
">
                                        <?php if (isset($_smarty_tpl->tpl_vars['_post']->value['module'])&&$_smarty_tpl->tpl_vars['_post']->value['module']==$_smarty_tpl->tpl_vars['mod_dir']->value) {?>
                                            <div class="item-row item-row-active">
                                        <?php } else { ?>
                                            <div class="item-row">
                                        <?php }?>
                                            <div class="item-icon">
                                                <img src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/modules/<?php echo $_smarty_tpl->tpl_vars['mod_dir']->value;?>
/icon.png" width="40" height="40" alt="<?php echo $_smarty_tpl->tpl_vars['_mod']->value['module_name'];?>
">
                                            </div>
                                            <div class="item-entry"><?php echo $_smarty_tpl->tpl_vars['_mod']->value['module_name'];?>
</div>
                                            <div class="item-enabled">
                                            <?php if ($_smarty_tpl->tpl_vars['_mod']->value['module_active']!='1') {?>
                                                <span class="item-disabled" title="module is currently disabled">D</span>
                                            <?php }?>
                                            </div>
                                        </div>
                                        </a>
                                    <?php } ?>

                                </dd>
                            <?php } ?>
                        </dl>
                    <?php } else { ?>
                        <div class="accordion">

                            <dt class="page_section_header admin_section_header">
                            <input type="text" value="search" name="ss" class="form_text form_admin_search" style="width:90%" onfocus="if(this.value=='search') { this.value = ''; }" onblur="if(this.value=='') { this.value = 'search'; }" onkeypress="if(event && event.keyCode == 13 && this.value.length > 0) { jrCore_window_location('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['curl']->value;?>
/search/sa=skin/skin=<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
/ss='+ jrE(this.value));return false; };">
                            </dt>

                            <dt class="page_section_header">skins</dt>
                            <div style="padding:3px 0">
                                
                                <?php  $_smarty_tpl->tpl_vars["_skin"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["_skin"]->_loop = false;
 $_smarty_tpl->tpl_vars["skin_dir"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['_skins']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["_skin"]->key => $_smarty_tpl->tpl_vars["_skin"]->value) {
$_smarty_tpl->tpl_vars["_skin"]->_loop = true;
 $_smarty_tpl->tpl_vars["skin_dir"]->value = $_smarty_tpl->tpl_vars["_skin"]->key;
?>
                                    <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_admin/info/skin=<?php echo $_smarty_tpl->tpl_vars['skin_dir']->value;?>
">
                                    <?php if ((isset($_smarty_tpl->tpl_vars['_post']->value['skin'])&&$_smarty_tpl->tpl_vars['_post']->value['skin']==$_smarty_tpl->tpl_vars['skin_dir']->value)||(!isset($_smarty_tpl->tpl_vars['_post']->value['skin'])&&$_smarty_tpl->tpl_vars['skin_dir']->value==$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'])) {?>
                                    <div class="item-row item-row-active">
                                    <?php } else { ?>
                                    <div class="item-row">
                                    <?php }?>
                                        <div class="item-icon">
                                            <img src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/skins/<?php echo $_smarty_tpl->tpl_vars['skin_dir']->value;?>
/icon.png" width="40" height="40" alt="<?php echo $_smarty_tpl->tpl_vars['_skin']->value['name'];?>
">
                                        </div>
                                        <?php if (isset($_smarty_tpl->tpl_vars['_skin']->value['title'])) {?>
                                        <div class="item-entry"><?php echo $_smarty_tpl->tpl_vars['_skin']->value['title'];?>
</div>
                                        <?php } else { ?>
                                        <div class="item-entry"><?php echo $_smarty_tpl->tpl_vars['_skin']->value['name'];?>
</div>
                                        <?php }?>
                                        <div class="item-enabled"></div>
                                    </div>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php }?>

                </div>
            </div>
        </div>

        <div class="col9 last">
            <div id="item-work">
                <?php echo $_smarty_tpl->tpl_vars['admin_page_content']->value;?>

            </div>
        </div>

    </div>
</div>
<?php }} ?>
