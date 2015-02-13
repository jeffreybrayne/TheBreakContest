<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:31:11
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/0197c427732f0090cad66d101893925d.tpl" */ ?>
<?php /*%%SmartyHeaderCode:207912718454d6760f66cef0-89015116%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fc9f87ad774e5636a4808211ef32a2c83d04377a' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/0197c427732f0090cad66d101893925d.tpl',
      1 => 1423341071,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '207912718454d6760f66cef0-89015116',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_conf' => 0,
    'new_artists_template' => 0,
    '_post' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d6760f744702_78889893',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d6760f744702_78889893')) {function content_54d6760f744702_78889893($_smarty_tpl) {?>
<?php $_smarty_tpl->_capture_stack[0][] = array("row_template", "new_artists_template", null); ob_start(); ?>

{if isset($_items)}
<div class="container">
    {foreach from=$_items item="row"}
    {if $row@first || ($row@iteration % 6) == 1}
    <div class="row">
    {/if}
        <div class="col2{if $row@last || ($row@iteration % 6) == 0} last{/if}">
            <div class="center" style="padding:10px;">
                <a href="{$jamroom_url}/{$row.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$row._profile_id size="medium" crop="auto" alt=$row.profile_name title=$row.profile_name class="iloutline img_shadow img_scale" style="max-width:190px;"}</a>
            </div>
        </div>
    {if $row@last || ($row@iteration % 6) == 0}
    </div>
    {/if}
    {/foreach}
</div>
{if $info.total_pages > 1}
<div style="float:left; padding-top:9px;padding-bottom:9px;">
    {if $info.prev_page > 0}
    <span class="button-arrow-previous" onclick="jrLoad('#newest_artists','{$info.page_base_url}/p={$info.prev_page}');$('html, body').animate({ scrollTop: $('#newartists').offset().top -100 }, 'slow');return false;">&nbsp;</span>
    {else}
    <span class="button-arrow-previous-off">&nbsp;</span>
    {/if}
    {if $info.next_page > 1}
    <span class="button-arrow-next" onclick="jrLoad('#newest_artists','{$info.page_base_url}/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#newartists').offset().top -100 }, 'slow');return false;">&nbsp;</span>
    {else}
    <span class="button-arrow-next-off">&nbsp;</span>
    {/if}
</div>
{/if}

<div style="float:right; padding-top:9px;">
    <a href="{$jamroom_url}/artists" title="More Artists"><div class="button-more">&nbsp;</div></a>
</div>

<div class="clear"> </div>
{/if}

<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>


<?php if (isset($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_require_images'])&&$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_require_images']=='on') {?>
    <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrProfile",'order_by'=>"_created desc",'search1'=>"profile_active = 1",'quota_id'=>$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_artist_quota'],'template'=>$_smarty_tpl->tpl_vars['new_artists_template']->value,'require_image'=>"profile_image",'pagebreak'=>"6",'page'=>$_smarty_tpl->tpl_vars['_post']->value['p']),$_smarty_tpl); } ?>

<?php } else { ?>
    <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrProfile",'order_by'=>"_created desc",'search1'=>"profile_active = 1",'quota_id'=>$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_artist_quota'],'template'=>$_smarty_tpl->tpl_vars['new_artists_template']->value,'pagebreak'=>"6",'page'=>$_smarty_tpl->tpl_vars['_post']->value['p']),$_smarty_tpl); } ?>

<?php }?>
<?php }} ?>
