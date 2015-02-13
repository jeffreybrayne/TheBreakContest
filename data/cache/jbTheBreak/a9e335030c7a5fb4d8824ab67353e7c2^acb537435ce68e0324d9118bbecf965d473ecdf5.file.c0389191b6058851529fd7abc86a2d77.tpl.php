<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:32:01
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/c0389191b6058851529fd7abc86a2d77.tpl" */ ?>
<?php /*%%SmartyHeaderCode:67665607554d676411dd963-73248557%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'acb537435ce68e0324d9118bbecf965d473ecdf5' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/c0389191b6058851529fd7abc86a2d77.tpl',
      1 => 1423341121,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '67665607554d676411dd963-73248557',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'type' => 0,
    'name' => 0,
    'label' => 0,
    'sublabel' => 0,
    'html' => 0,
    'theme' => 0,
    'help' => 0,
    'show_update_in_help' => 0,
    'default' => 0,
    'default_label' => 0,
    '_conf' => 0,
    '_post' => 0,
    'updated' => 0,
    'user' => 0,
    'value' => 0,
    'default_value' => 0,
    'saved_value' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d676413fc840_36271498',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d676413fc840_36271498')) {function content_54d676413fc840_36271498($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_truncate')) include '/home/twinli5/public_html/break/modules/jrCore/contrib/smarty/libs/plugins/modifier.truncate.php';
?><tr>
  <td class="element_left form_input_left <?php echo $_smarty_tpl->tpl_vars['type']->value;?>
_left <?php echo $_smarty_tpl->tpl_vars['name']->value;?>
_element_left">
    <a id="ff-<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
"></a><?php echo $_smarty_tpl->tpl_vars['label']->value;?>

    <?php if (isset($_smarty_tpl->tpl_vars['sublabel']->value)&&strlen($_smarty_tpl->tpl_vars['sublabel']->value)>0) {?>
      <br><span class="sublabel"><?php echo $_smarty_tpl->tpl_vars['sublabel']->value;?>
</span>
    <?php }?>
  </td>
  <td class="element_right form_input_right <?php echo $_smarty_tpl->tpl_vars['type']->value;?>
_right <?php echo $_smarty_tpl->tpl_vars['name']->value;?>
_element_right" style="position:relative">
    <?php echo $_smarty_tpl->tpl_vars['html']->value;?>

    <?php if ($_smarty_tpl->tpl_vars['type']->value=='textarea'&&!isset($_smarty_tpl->tpl_vars['theme']->value)) {?>
        <a onclick="var e=$(this).prev();var h=e.height() + 100;e.animate( { height: h +'px' } , 250);"><?php if (function_exists('smarty_function_jrCore_icon')) { echo smarty_function_jrCore_icon(array('icon'=>"arrow-down",'size'=>"16"),$_smarty_tpl); } ?>
</a>
    <?php }?>
    <?php if (isset($_smarty_tpl->tpl_vars['help']->value)&&strlen($_smarty_tpl->tpl_vars['help']->value)>0) {?>
      <input type="button" value="?" class="form_button form_help_button" title="<?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrCore",'id'=>34,'default'=>"expand help"),$_smarty_tpl); } ?>
" onclick="$('#h_<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
').slideToggle(250);">
    <?php }?>
  </td>
</tr>
<?php if (isset($_smarty_tpl->tpl_vars['help']->value)&&strlen($_smarty_tpl->tpl_vars['help']->value)>0&&$_smarty_tpl->tpl_vars['type']->value!='editor') {?>
<tr>
  <td class="element_left form_input_left" style="padding:0;height:0px"></td>
  <td>
    <div id="h_<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
" class="form_help" style="display:none">

      <table class="form_help_drop">
        <tr>
          <td class="form_help_drop_left">
            <?php echo $_smarty_tpl->tpl_vars['help']->value;?>

            
            <?php if (isset($_smarty_tpl->tpl_vars['show_update_in_help']->value)&&$_smarty_tpl->tpl_vars['show_update_in_help']->value=='1') {?>
              <?php if (isset($_smarty_tpl->tpl_vars['default']->value)&&!is_array($_smarty_tpl->tpl_vars['default']->value)&&strlen($_smarty_tpl->tpl_vars['default']->value)>0) {?>
                <?php if (isset($_smarty_tpl->tpl_vars['default_label']->value)) {?>
                
                <br><span class="form_help_default">Default: <?php echo $_smarty_tpl->tpl_vars['default_label']->value;?>
</span>
                <?php } else { ?>
                <br><span class="form_help_default">Default: <?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['default']->value,60);?>
</span>
                <?php }?>
              <?php }?>
              <?php if ($_smarty_tpl->tpl_vars['_conf']->value['jrDeveloper_developer_mode']=='on'&&strpos($_smarty_tpl->tpl_vars['_post']->value['_uri'],'global')) {?>
                  <br><br>Template Variable: {&#36;_conf.<?php echo $_smarty_tpl->tpl_vars['_post']->value['module'];?>
_<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
}
              <?php }?>
              <?php if (isset($_smarty_tpl->tpl_vars['updated']->value)&&$_smarty_tpl->tpl_vars['updated']->value>0) {?>
                <br><span class="form_help_small">Last Updated: <?php echo smarty_modifier_jrCore_date_format($_smarty_tpl->tpl_vars['updated']->value);?>
 by <?php echo (($tmp = @$_smarty_tpl->tpl_vars['user']->value)===null||strlen($tmp)===0 ? "installer" : $tmp);?>
</span>
              <?php }?>
            <?php }?>
          </td>
          <td class="form_help_drop_right">
            
            <?php if (isset($_smarty_tpl->tpl_vars['show_update_in_help']->value)&&$_smarty_tpl->tpl_vars['show_update_in_help']->value=='1') {?>
              <?php if (isset($_smarty_tpl->tpl_vars['default']->value)&&!is_array($_smarty_tpl->tpl_vars['default']->value)&&strlen($_smarty_tpl->tpl_vars['default']->value)>0&&$_smarty_tpl->tpl_vars['default']->value!=$_smarty_tpl->tpl_vars['value']->value) {?>
                
                <?php if (isset($_smarty_tpl->tpl_vars['type']->value)&&$_smarty_tpl->tpl_vars['type']->value=='checkbox') {?>
                  <?php if (isset($_smarty_tpl->tpl_vars['default']->value)&&$_smarty_tpl->tpl_vars['default']->value=="on") {?>
                    <input type="button" value="use default" class="form_button" style="width:100px" onclick="$('#<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
').prop('checked','checked');">
                  <?php } else { ?>
                    <input type="button" value="use default" class="form_button" style="width:100px" onclick="$('#<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
').prop('checked','');">
                  <?php }?>
                <?php } else { ?>
                  <input type="button" value="use default" class="form_button" style="width:100px;" onclick="var v=$(this).val();if (v == 'use default'){$('#<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
').val('<?php echo $_smarty_tpl->tpl_vars['default_value']->value;?>
');$(this).val('cancel');}else{$('#<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
').val('<?php echo $_smarty_tpl->tpl_vars['saved_value']->value;?>
');$(this).val('use default');}">
                <?php }?>
              <?php }?>
            <?php }?>
          </td>
        </tr>
      </table>

    </div>
  </td>
</tr>
<?php }?>
<?php }} ?>
