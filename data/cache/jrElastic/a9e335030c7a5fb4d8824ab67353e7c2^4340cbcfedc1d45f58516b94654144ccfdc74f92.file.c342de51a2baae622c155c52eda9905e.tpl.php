<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:21:47
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/c342de51a2baae622c155c52eda9905e.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8919893954d673db02c045-95864689%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4340cbcfedc1d45f58516b94654144ccfdc74f92' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/c342de51a2baae622c155c52eda9905e.tpl',
      1 => 1423340507,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8919893954d673db02c045-95864689',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_conf' => 0,
    'jamroom_url' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d673db0a83b8_42217938',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d673db0a83b8_42217938')) {function content_54d673db0a83b8_42217938($_smarty_tpl) {?><?php if (function_exists('smarty_function_jrCore_include')) { echo smarty_function_jrCore_include(array('template'=>"meta.tpl"),$_smarty_tpl); } ?>


<body>

<?php if (jrCore_is_mobile_device()) {?>
    <?php if (function_exists('smarty_function_jrCore_include')) { echo smarty_function_jrCore_include(array('template'=>"header_menu_mobile.tpl"),$_smarty_tpl); } ?>

<?php }?>

<div id="header">
    <div id="header_content">

        
        <?php if (jrCore_is_mobile_device()) {?>
            <div id="main_logo">
                <?php if (function_exists('smarty_function_jrCore_image')) { echo smarty_function_jrCore_image(array('id'=>"mmt",'skin'=>"jrElastic",'image'=>"menu.png",'alt'=>"menu"),$_smarty_tpl); } ?>

                <?php if (function_exists('smarty_function_jrCore_image')) { echo smarty_function_jrCore_image(array('image'=>"logo.png",'width'=>"170",'height'=>"40",'class'=>"jlogo",'alt'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'],'custom'=>"logo"),$_smarty_tpl); } ?>

            </div>
        <?php } else { ?>
            <div id="main_logo">
                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
"><?php if (function_exists('smarty_function_jrCore_image')) { echo smarty_function_jrCore_image(array('image'=>"logo.png",'width'=>"191",'height'=>"44",'class'=>"jlogo",'alt'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'],'custom'=>"logo"),$_smarty_tpl); } ?>
</a>
            </div>
            <?php if (function_exists('smarty_function_jrCore_include')) { echo smarty_function_jrCore_include(array('template'=>"header_menu_desktop.tpl"),$_smarty_tpl); } ?>


        <?php }?>

    </div>

</div>


<div id="searchform" class="search_box" style="display:none;">
    <?php if (function_exists('smarty_function_jrSearch_form')) { echo smarty_function_jrSearch_form(array('class'=>"form_text",'value'=>"Search Site",'style'=>"width:70%"),$_smarty_tpl); } ?>

    <div style="float:right;clear:both;margin-top:3px;">
        <a class="simplemodal-close"><?php if (function_exists('smarty_function_jrCore_icon')) { echo smarty_function_jrCore_icon(array('icon'=>"close",'size'=>20),$_smarty_tpl); } ?>
</a>
    </div>
    <div class="clear"></div>
</div>

<div id="wrapper">
    <div id="content">

        <noscript>
            <div class="item error center" style="margin:12px">
                This site requires Javascript to function properly - please enable Javascript in your browser
            </div>
        </noscript>

        <!-- end header.tpl -->
<?php }} ?>
