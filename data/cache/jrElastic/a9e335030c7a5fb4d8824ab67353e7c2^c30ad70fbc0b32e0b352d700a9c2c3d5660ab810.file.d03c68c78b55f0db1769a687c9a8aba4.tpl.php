<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:22:01
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/d03c68c78b55f0db1769a687c9a8aba4.tpl" */ ?>
<?php /*%%SmartyHeaderCode:188751633154d673e9408ae5-13333265%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c30ad70fbc0b32e0b352d700a9c2c3d5660ab810' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/d03c68c78b55f0db1769a687c9a8aba4.tpl',
      1 => 1423340521,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '188751633154d673e9408ae5-13333265',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_profile_id' => 0,
    'jamroom_url' => 0,
    'furl' => 0,
    'murl' => 0,
    'profile_url' => 0,
    'title' => 0,
    '_post' => 0,
    'timeline' => 0,
    'mention' => 0,
    'search' => 0,
    'svar' => 0,
    'page_num' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d673e9564fa1_59304673',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d673e9564fa1_59304673')) {function content_54d673e9564fa1_59304673($_smarty_tpl) {?><div class="block">

<?php if (jrProfile_is_profile_owner($_smarty_tpl->tpl_vars['_profile_id']->value)) {?>

    <div class="title">

        <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrAction",'assign'=>"murl"),$_smarty_tpl); } ?>


        <?php if (jrCore_module_is_active('jrFeed')) {?>
            <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrFeed",'assign'=>"furl"),$_smarty_tpl); } ?>

            <?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"31",'default'=>"activity feed",'assign'=>"title"),$_smarty_tpl); } ?>

            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['furl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['profile_url']->value;?>
" title="<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
"><?php if (function_exists('smarty_function_jrCore_icon')) { echo smarty_function_jrCore_icon(array('icon'=>"rss",'size'=>"20"),$_smarty_tpl); } ?>
</a>&nbsp;
        <?php }?>

        <?php if (isset($_smarty_tpl->tpl_vars['_post']->value['profile_actions'])&&$_smarty_tpl->tpl_vars['_post']->value['profile_actions']=='mentions') {?>

        <h2><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['profile_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/mentions" title="timeline"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"7",'default'=>"Mentions"),$_smarty_tpl); } ?>
</a></h2>
        <div style="float:right">
            <?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"4",'default'=>"Timeline",'assign'=>"timeline"),$_smarty_tpl); } ?>

            <h3><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['profile_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/timeline" title="<?php echo $_smarty_tpl->tpl_vars['timeline']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['timeline']->value;?>
</a></h3>&nbsp; &bull;

        <?php } else { ?>

        <h2><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['profile_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
" title="timeline"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"4",'default'=>"Timeline"),$_smarty_tpl); } ?>
</a></h2>
        <div style="float:right">
            <?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"7",'default'=>"Mentions",'assign'=>"mention"),$_smarty_tpl); } ?>

            <h3><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['profile_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/mentions" title="<?php echo $_smarty_tpl->tpl_vars['mention']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['mention']->value;?>
</a></h3>&nbsp; &bull;

        <?php }?>

        <?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"8",'default'=>"Search",'assign'=>"search"),$_smarty_tpl); } ?>

        <h3>&nbsp;<a href="" onclick="$('#action_search').slideToggle(300);return false" title="<?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['search']->value);?>
"><?php echo $_smarty_tpl->tpl_vars['search']->value;?>
</a>&nbsp;&nbsp;<a href="" onclick="$('#action_search').slideToggle(300);return false" title="<?php echo $_smarty_tpl->tpl_vars['search']->value;?>
"><?php if (function_exists('smarty_function_jrCore_icon')) { echo smarty_function_jrCore_icon(array('icon'=>"arrow-down",'size'=>"20"),$_smarty_tpl); } ?>
</a></h3>
        </div>

    </div>

    <div class="block_content">

        <div id="action_search" class="item left p10" style="display:none">
            <?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"8",'default'=>"Search",'assign'=>"svar"),$_smarty_tpl); } ?>

            <form name="action_search_form" action="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['profile_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/search" method="get" style="margin-bottom:0">
                <input type="text" name="ss" value="<?php echo $_smarty_tpl->tpl_vars['svar']->value;?>
" class="form_text" onfocus="if(this.value=='<?php echo $_smarty_tpl->tpl_vars['svar']->value;?>
'){ this.value=''; }" onblur="if(this.value==''){ this.value='<?php echo $_smarty_tpl->tpl_vars['svar']->value;?>
'; }">&nbsp;
                <input type="submit" class="form_button" value="<?php echo $_smarty_tpl->tpl_vars['search']->value;?>
">
            </form>
        </div>

        
        <?php if (jrUser_is_linked_to_profile($_smarty_tpl->tpl_vars['_profile_id']->value)) {?>
            <div id="new_action" class="item">
                <small><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"3",'default'=>"Post a new Activity Update"),$_smarty_tpl); } ?>
:</small><br>
                <?php if (function_exists('smarty_function_jrAction_form')) { echo smarty_function_jrAction_form(array(),$_smarty_tpl); } ?>

            </div>
        <?php }?>


        
        <div class="item">

            <div id="timeline">

                <?php $_smarty_tpl->tpl_vars["page_num"] = new Smarty_variable("1", null, 0);?>
                <?php if (isset($_smarty_tpl->tpl_vars['_post']->value['p'])) {?>
                    <?php $_smarty_tpl->tpl_vars["page_num"] = new Smarty_variable($_smarty_tpl->tpl_vars['_post']->value['p'], null, 0);?>
                <?php }?>

                
                <?php if (isset($_smarty_tpl->tpl_vars['_post']->value['profile_actions'])&&$_smarty_tpl->tpl_vars['_post']->value['profile_actions']=='mentions') {?>
                    <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrAction",'search1'=>"_profile_id != ".((string)$_smarty_tpl->tpl_vars['_profile_id']->value),'search2'=>"action_text regexp @".((string)$_smarty_tpl->tpl_vars['profile_url']->value)."[[:>:]]",'order_by'=>"_item_id numerical_desc",'pagebreak'=>"12",'page'=>$_smarty_tpl->tpl_vars['page_num']->value,'pager'=>true),$_smarty_tpl); } ?>

                <?php } elseif (isset($_smarty_tpl->tpl_vars['_post']->value['profile_actions'])&&$_smarty_tpl->tpl_vars['_post']->value['profile_actions']=='search') {?>
                    <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrAction",'search'=>"_item_id in ".((string)$_smarty_tpl->tpl_vars['_post']->value['match_ids']),'order_by'=>"_item_id numerical_desc",'pagebreak'=>"12",'page'=>$_smarty_tpl->tpl_vars['page_num']->value,'pager'=>true),$_smarty_tpl); } ?>

                <?php } else { ?>
                    
                    <?php if (jrUser_is_linked_to_profile($_smarty_tpl->tpl_vars['_profile_id']->value)) {?>
                        <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrAction",'profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'include_followed'=>true,'order_by'=>"_item_id numerical_desc",'pagebreak'=>"12",'page'=>$_smarty_tpl->tpl_vars['page_num']->value,'pager'=>true,'no_cache'=>true),$_smarty_tpl); } ?>

                    <?php } else { ?>
                        <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrAction",'profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'order_by'=>"_item_id numerical_desc",'pagebreak'=>"12",'page'=>$_smarty_tpl->tpl_vars['page_num']->value,'pager'=>true),$_smarty_tpl); } ?>

                    <?php }?>
                <?php }?>

            </div>

        </div>

    </div>

<?php } else { ?>

    <div class="title">
        <h2><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"4",'default'=>"Profile Updates"),$_smarty_tpl); } ?>
</h2>
        <?php if (jrCore_module_is_active('jrFeed')) {?>
            <div style="float:right">
                <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrFeed",'assign'=>"furl"),$_smarty_tpl); } ?>

                <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrAction",'assign'=>"murl"),$_smarty_tpl); } ?>

                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['furl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['profile_url']->value;?>
"><?php if (function_exists('smarty_function_jrCore_icon')) { echo smarty_function_jrCore_icon(array('icon'=>"rss",'size'=>"20"),$_smarty_tpl); } ?>
</a>
            </div>
        <?php }?>
    </div>

    <div class="block_content">
        <div class="item">
            <div id="timeline">
                <?php if (isset($_smarty_tpl->tpl_vars['_post']->value['p'])) {?>
                    <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrAction",'profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'order_by'=>"_item_id numerical_desc",'pagebreak'=>"12",'page'=>$_smarty_tpl->tpl_vars['_post']->value['p'],'pager'=>true),$_smarty_tpl); } ?>

                <?php } else { ?>
                    <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrAction",'profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'order_by'=>"_item_id numerical_desc",'pagebreak'=>"12",'page'=>"1",'pager'=>true),$_smarty_tpl); } ?>

                <?php }?>
            </div>
        </div>
    </div>

<?php }?>

</div>
<?php }} ?>
