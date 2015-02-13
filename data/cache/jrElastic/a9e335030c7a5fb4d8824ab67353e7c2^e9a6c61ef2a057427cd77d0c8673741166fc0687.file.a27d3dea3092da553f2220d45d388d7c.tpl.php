<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:22:01
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/a27d3dea3092da553f2220d45d388d7c.tpl" */ ?>
<?php /*%%SmartyHeaderCode:796519254d673e994ac71-46736644%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e9a6c61ef2a057427cd77d0c8673741166fc0687' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/a27d3dea3092da553f2220d45d388d7c.tpl',
      1 => 1423340521,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '796519254d673e994ac71-46736644',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_profile_id' => 0,
    'jamroom_url' => 0,
    'profile_url' => 0,
    'profile_name' => 0,
    'profile_quota_id' => 0,
    'profile_disable_sidebar' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d673e99d4455_86799067',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d673e99d4455_86799067')) {function content_54d673e99d4455_86799067($_smarty_tpl) {?><?php if (function_exists('smarty_function_jrCore_include')) { echo smarty_function_jrCore_include(array('template'=>"header.tpl"),$_smarty_tpl); } ?>


<div class="container">

    <div class="row">
        <div class="col12 last">
            <div class="profile_name_box">

                <div class="block_config" style="margin-top:3px">
                    <?php if (function_exists('smarty_function_jrCore_module_function')) { echo smarty_function_jrCore_module_function(array('function'=>"jrFollower_button",'profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'title'=>"Follow This Profile"),$_smarty_tpl); } ?>

                    <?php if (function_exists('smarty_function_jrCore_item_update_button')) { echo smarty_function_jrCore_item_update_button(array('module'=>"jrProfile",'view'=>"settings/profile_id=".((string)$_smarty_tpl->tpl_vars['_profile_id']->value),'profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'item_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'title'=>"Update Profile"),$_smarty_tpl); } ?>

                    <?php if (jrUser_is_admin()||jrUser_is_power_user()) {?>
                        <?php if (function_exists('smarty_function_jrCore_item_create_button')) { echo smarty_function_jrCore_item_create_button(array('module'=>"jrProfile",'view'=>"create",'profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'title'=>"Create new Profile"),$_smarty_tpl); } ?>

                    <?php }?>
                    <?php if (jrUser_is_master()) {?>
                        <?php if (function_exists('smarty_function_jrCore_item_delete_button')) { echo smarty_function_jrCore_item_delete_button(array('module'=>"jrProfile",'view'=>"delete_save",'profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'item_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'title'=>"Delete this Profile",'prompt'=>"Are you sure you want to delete this profile?"),$_smarty_tpl); } ?>

                    <?php }?>
                </div>

                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['profile_url']->value;?>
"><h1 class="profile_name"><?php echo $_smarty_tpl->tpl_vars['profile_name']->value;?>
</h1></a>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col12 last">
            <div class="profile_menu">
                <?php if (jrCore_is_mobile_device()) {?>
                    <?php if (function_exists('smarty_function_jrProfile_menu')) { echo smarty_function_jrProfile_menu(array('template'=>"profile_menu_mobile.tpl",'profile_quota_id'=>$_smarty_tpl->tpl_vars['profile_quota_id']->value,'profile_url'=>$_smarty_tpl->tpl_vars['profile_url']->value),$_smarty_tpl); } ?>

                <?php } else { ?>
                    <?php if (function_exists('smarty_function_jrProfile_menu')) { echo smarty_function_jrProfile_menu(array('template'=>"profile_menu.tpl",'profile_quota_id'=>$_smarty_tpl->tpl_vars['profile_quota_id']->value,'profile_url'=>$_smarty_tpl->tpl_vars['profile_url']->value),$_smarty_tpl); } ?>

                <?php }?>
            </div>
        </div>
    </div>

    <div class="row">

    <?php if ($_smarty_tpl->tpl_vars['profile_disable_sidebar']->value!=1) {?>
        <?php if (function_exists('smarty_function_jrCore_include')) { echo smarty_function_jrCore_include(array('template'=>"profile_sidebar.tpl"),$_smarty_tpl); } ?>

    <?php }?>

    
<?php }} ?>
