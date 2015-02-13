<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:21:47
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/d251449592b923a6c96ede9b516d793e.tpl" */ ?>
<?php /*%%SmartyHeaderCode:31806727254d673dbefb908-23441621%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '37dc5fc240ebf046497640cb0d449923782a035f' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/d251449592b923a6c96ede9b516d793e.tpl',
      1 => 1423340507,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '31806727254d673dbefb908-23441621',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_conf' => 0,
    'jamroom_url' => 0,
    'css_footer_href' => 0,
    '_css' => 0,
    'javascript_footer_href' => 0,
    '_js' => 0,
    'javascript_footer_function' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d673dc06d2e9_77175156',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d673dc06d2e9_77175156')) {function content_54d673dc06d2e9_77175156($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/home/twinli5/public_html/break/modules/jrCore/contrib/smarty/libs/plugins/modifier.date_format.php';
?></div>
</div>

<div id="footer">
    <div id="footer_content">
        <div class="container">

            <div class="row">
                
                <div class="col6">
                    <div id="footer_sn">

                        
                        <?php if (strlen($_smarty_tpl->tpl_vars['_conf']->value['jrElastic_twitter_name'])>0) {?>
                            <a href="https://twitter.com/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrElastic_twitter_name'];?>
"><?php if (function_exists('smarty_function_jrCore_image')) { echo smarty_function_jrCore_image(array('image'=>"sn-twitter.png",'width'=>"40",'height'=>"40",'class'=>"social-img",'alt'=>"twitter",'title'=>"Follow @".((string)$_smarty_tpl->tpl_vars['_conf']->value['jrElastic_twitter_name'])),$_smarty_tpl); } ?>
</a>
                        <?php }?>

                        <?php if (strlen($_smarty_tpl->tpl_vars['_conf']->value['jrElastic_facebook_name'])>0) {?>
                            <a href="https://facebook.com/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrElastic_facebook_name'];?>
"><?php if (function_exists('smarty_function_jrCore_image')) { echo smarty_function_jrCore_image(array('image'=>"sn-facebook.png",'width'=>"40",'height'=>"40",'class'=>"social-img",'alt'=>"facebook",'title'=>"Like ".((string)$_smarty_tpl->tpl_vars['_conf']->value['jrElastic_facebook_name'])." on Facebook"),$_smarty_tpl); } ?>
</a>
                        <?php }?>

                        <?php if (strlen($_smarty_tpl->tpl_vars['_conf']->value['jrElastic_linkedin_name'])>0) {?>
                            <a href="https://linkedin.com/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrElastic_linkedin_name'];?>
"><?php if (function_exists('smarty_function_jrCore_image')) { echo smarty_function_jrCore_image(array('image'=>"sn-linkedin.png",'width'=>"40",'height'=>"40",'class'=>"social-img",'alt'=>"linkedin",'title'=>"Link up with ".((string)$_smarty_tpl->tpl_vars['_conf']->value['jrElastic_linkedin_name'])." on LinkedIn"),$_smarty_tpl); } ?>
</a>
                        <?php }?>

                        <?php if (strlen($_smarty_tpl->tpl_vars['_conf']->value['jrElastic_google_name'])>0) {?>
                            <a href="https://google.com/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrElastic_google_name'];?>
"><?php if (function_exists('smarty_function_jrCore_image')) { echo smarty_function_jrCore_image(array('image'=>"sn-google-plus.png",'width'=>"40",'height'=>"40",'class'=>"social-img",'alt'=>"google+",'title'=>"Follow ".((string)$_smarty_tpl->tpl_vars['_conf']->value['jrElastic_google_name'])." on Google+"),$_smarty_tpl); } ?>
</a>
                        <?php }?>

                    </div>
                </div>

                
                <div class="col6 last">
                    <div id="footer_text">
                        &copy;<?php echo smarty_modifier_date_format(time(),"%Y");?>
 <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'];?>
</a><br>
                        
                        <?php if (function_exists('smarty_function_jrCore_powered_by')) { echo smarty_function_jrCore_powered_by(array(),$_smarty_tpl); } ?>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php if (isset($_smarty_tpl->tpl_vars['css_footer_href']->value)) {?>
    <?php  $_smarty_tpl->tpl_vars["_css"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["_css"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['css_footer_href']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["_css"]->key => $_smarty_tpl->tpl_vars["_css"]->value) {
$_smarty_tpl->tpl_vars["_css"]->_loop = true;
?>
        <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['_css']->value['source'];?>
" media="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['_css']->value['media'])===null||strlen($tmp)===0 ? "screen" : $tmp);?>
"/>
    <?php } ?>
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['javascript_footer_href']->value)) {?>
    <?php  $_smarty_tpl->tpl_vars["_js"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["_js"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['javascript_footer_href']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["_js"]->key => $_smarty_tpl->tpl_vars["_js"]->value) {
$_smarty_tpl->tpl_vars["_js"]->_loop = true;
?>
        <script type="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['_js']->value['type'])===null||strlen($tmp)===0 ? "text/javascript" : $tmp);?>
" src="<?php echo $_smarty_tpl->tpl_vars['_js']->value['source'];?>
"></script>
    <?php } ?>
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['javascript_footer_function']->value)) {?>
    <script type="text/javascript">
        <?php echo $_smarty_tpl->tpl_vars['javascript_footer_function']->value;?>

    </script>
<?php }?>


<div id="jr_temp_work_div" style="display:none"></div>

<?php if (jrCore_is_mobile_device()) {?>

    
    <script type="text/javascript">
    (function($) {
        $(document).ready(function() {
            var ms = new $.slidebars();
            $('#mmt').on('click', function() {
                ms.slidebars.open('left');
            });
        });
    }) (jQuery);
    </script>

<?php } else { ?>

    
    <script type="text/javascript">
        $(function () {
            $('#menu-wrap').prepend('<div id="menu-trigger"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"20",'default'=>"menu"),$_smarty_tpl); } ?>
</div>');
            $("#menu-trigger").on("click", function () {
                $("#menu").slideToggle();

            });
            var isiPad = navigator.userAgent.match(/iPad/i) != null;
            if (isiPad) $('#menu ul').addClass('no-transition');
        });
    </script>

<?php }?>

</body>
</html>
<?php }} ?>
