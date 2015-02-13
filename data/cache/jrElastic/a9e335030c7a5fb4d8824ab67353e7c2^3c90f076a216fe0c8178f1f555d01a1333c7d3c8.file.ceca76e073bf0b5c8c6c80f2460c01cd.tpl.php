<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:22:13
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/ceca76e073bf0b5c8c6c80f2460c01cd.tpl" */ ?>
<?php /*%%SmartyHeaderCode:100016864654d673f5450589-14905199%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3c90f076a216fe0c8178f1f555d01a1333c7d3c8' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/ceca76e073bf0b5c8c6c80f2460c01cd.tpl',
      1 => 1423340533,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '100016864654d673f5450589-14905199',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'label' => 0,
    'html' => 0,
    'show_help' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d673f5468352_81471212',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d673f5468352_81471212')) {function content_54d673f5468352_81471212($_smarty_tpl) {?><tr>
  <td class="element_left search_area_left"><?php echo $_smarty_tpl->tpl_vars['label']->value;?>
</td>
  <td class="element_right search_area_right">
    <?php echo $_smarty_tpl->tpl_vars['html']->value;?>

    <?php if ($_smarty_tpl->tpl_vars['show_help']->value=='1') {?>
    <input type="button" value="?" class="form_button" title="<?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrCore",'id'=>34,'default'=>"expand help"),$_smarty_tpl); } ?>
" onclick="$('#search_help').slideToggle(250);">
    <?php }?>
  </td>
</tr>
<?php if ($_smarty_tpl->tpl_vars['show_help']->value=='1') {?>
<tr>
  <td class="element_left form_input_left" style="padding:0;height:0"></td>
  <td>
    <div id="search_help" class="form_help" style="display:none;">

      <table class="form_help_drop">
        <tr>
          <td class="form_help_drop_left">
            Item Search Options:<br>
            <b>value</b> - Search for <b>exact</b> value match.<br>
            <b>%value</b> - Search for items that <b>end in</b> value.<br>
            <b>value%</b> - Search for items that <b>begin with</b> value.<br>
            <b>%value%</b> - Search for items that <b>contain</b> value.<br><br>
            Item Key Search Options:<br>
            <b>key:value</b> - Search for specific key with exact value match.<br>
            <b>key:%value</b> - Search for specific key that <b>begins with</b> value.<br>
            <b>key:value%</b> - Search for specific key that <b>ends with</b> value.<br>
            <b>key:%value%</b> - Search for specific key that <b>contains</b> value.
          </td>
        </tr>
      </table>

    </div>
  </td>
</tr>
<?php }?>
<?php }} ?>
