{jrCore_module_url module="jrMarket" assign="murl"}
{if isset($_items)}
<script src="https://checkout.stripe.com/v2/checkout.js" type="text/javascript"></script>
{foreach from=$_items item="item"}
<div class="item">
    <div class="container">
        <div class="row">

            {if $type == 'Module' || $type == 'Skin' || $type == 'Installed'}

            <div class="col2">
                <div class="block_image" style="text-align:center">
                    <img src="{$item.market_image_url}" width="128">
                </div>
            </div>

            <div class="col7">
                <div style="padding:0 10px">

                    <h2>{$item.market_title}</h2>
                    <br>

                    {* we show stable/beta/channel if subscribed *}
                    {if $show_item_status == '1'}
                        {if $item.market_channel == 'stable' || $item.market_channel == 'beta'}
                            <span class="status_section status_section_{$item.market_channel} market-version">version {$item.market_version|regex_replace:"/[^0-9.]/":''} &nbsp; {$item.market_channel|strtoupper}</span>&nbsp;
                        {else}
                            <span class="status_section market-version">version {$item.market_version} &nbsp; PRIVATE</span>&nbsp;
                        {/if}
                    {/if}

                    <small>by <a href="{$item.profile_full_url}" target="_blank">@{$item.profile_url}</a></small><br>
                    <span class="market-description">{$item.market_description|truncate:190}</span><br><a href="{$item.market_detail_url}" target="_blank">more info...</a>
                </div>
            </div>

            <div class="col3 last">
                <div style="padding:0 10px;text-align:center;white-space:nowrap">

                    <span style="display:inline-block;margin-bottom:10px">
                    {if $item.market_file_item_price > 0}

                        <span style="display:inline-block;margin-bottom:10px">
                        {if isset($item.market_allow_license_install) && $item.market_allow_license_install == '1'}
                            you have a license<br>
                            <h3>Free</h3>

                        {elseif isset($item.market_user_promo_code) && $item.market_user_promo_code == '1' && $item.market_already_installed != '1' && $type != 'Installed'}
                            promo code applied<br>
                            <h3>&#36;{$item.market_file_item_price|number_format:2}&nbsp;&nbsp;&nbsp;<strike>&#36;{$item.market_file_item_original_price|number_format:2}</strike></h3>

                        {else}

                            <h3>&#36;{$item.market_file_item_price|number_format:2}</h3>

                        {/if}
                        </span><br>

                        {if $item.market_already_installed == '1' && !isset($_post.sli)}

                            {if $type == 'Module'}
                            <input type="button" class="form_button form_button_disabled" style="width:150px" value="Already Installed" onclick="window.location='{$jamroom_url}/{jrCore_module_url module=$item.market_name}/admin/info'">
                            {else}
                            <input type="button" class="form_button form_button_disabled" style="width:150px" value="Already Installed" onclick="window.location='{$jamroom_url}/{jrCore_module_url module="jrCore"}/skin_admin/info/skin={$item.market_name}'">
                            {/if}

                        {elseif isset($item.market_allow_license_install) && $item.market_allow_license_install == '1'}

                            <img id="fsi_{$item._item_id}" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/submit.gif" width="24" height="24" style="display:none" alt="{jrCore_lang module="jrCore" id="73" default="working..."}">&nbsp;<input type="button" class="form_button" style="width:150px" value="install" onclick="if (confirm('You already own a license for this item - install?')) { jrMarket_quick_purchase('{$item.market_type}','{$item.market_file_item_price}','{$item._item_id}','{$item.market_name}'); }">

                        {elseif isset($quick_purchase_id) && strlen($quick_purchase_id) > 5 && isset($_conf.jrMarket_quick_purchase) && $_conf.jrMarket_quick_purchase == 'on'}

                            <img id="fsi_{$item._item_id}" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/submit.gif" width="24" height="24" style="display:none" alt="{jrCore_lang module="jrCore" id="73" default="working..."}">&nbsp;<input type="button" class="form_button" style="width:150px" value="quick purchase" onclick="if (confirm('Quick purchase and install this item for USD {$item.market_file_item_price|number_format:2}?')) { jrMarket_quick_purchase('{$item.market_type}','{$item.market_file_item_price}','{$item._item_id}','{$item.market_name}'); }">

                        {elseif !isset($active_market.system_email) || strlen($active_market.system_email) === 0 || !isset($active_market.system_code) || strlen($active_market.system_code) !== 32}

                            <input type="button" class="form_button" style="width:150px" value="purchase" onclick="window.location='{$jamroom_url}/{$murl}/config_check'">

                        {else}

                            {jrMarket_purchase_button item=$item key=$api_key}

                        {/if}

                    {else}

                        <span style="display:inline-block;margin-bottom:10px">
                        {if isset($item.market_user_promo_code) && $item.market_user_promo_code == '1' && $item.market_already_installed != '1' && $type != 'Installed'}
                            promo code applied<br>
                            <h3>&#36;{$item.market_file_item_price|number_format:2}&nbsp;&nbsp;&nbsp;<strike>&#36;{$item.market_file_item_original_price|number_format:2}</strike></h3>
                        {else}
                            <h3>Free</h3>
                        {/if}
                        </span><br>

                        {if $item.market_already_installed == '1'}

                            <input type="button" class="form_button form_button_disabled" style="width:150px" value="Already Installed" onclick="window.location='{$jamroom_url}/{jrCore_module_url module=$item.market_name}/admin/info'">

                        {elseif !isset($active_market.system_email) || strlen($active_market.system_email) === 0 || !isset($active_market.system_code) || strlen($active_market.system_code) !== 32}

                            <input type="button" class="form_button" style="width:150px" value="install" onclick="window.location='{$jamroom_url}/{$murl}/config_check'">

                        {else}

                            <img id="fsi_{$item._item_id}" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/submit.gif" width="24" height="24" style="display:none" alt="{jrCore_lang module="jrCore" id="73" default="working..."}">&nbsp;<input type="button" class="form_button" style="width:150px" value="install" onclick="if (confirm('Install this item?')) { jrMarket_install_item('{$item.market_type}','{$item._item_id}','{$item.market_name}'); }">

                        {/if}

                    {/if}
                    </span>
                    <br>

                    {* Screen shots *}
                    {if isset($item.market_screenshot_1_url)}
                        <a href="{$item.market_screenshot_1_url}/xxxlarge" data-lightbox="images" title="screenshot 1"><img src="{$item.market_screenshot_1_url}/size=xsmall/crop=auto" width="40" class="iloutline img_shadow"></a>
                    {/if}
                    {if isset($item.market_screenshot_2_url)}
                        <a href="{$item.market_screenshot_2_url}/xxxlarge" data-lightbox="images" title="screenshot 2"><img src="{$item.market_screenshot_2_url}/size=xsmall/crop=auto" width="40" class="iloutline img_shadow"></a>
                    {/if}
                    {if isset($item.market_screenshot_3_url)}
                        <a href="{$item.market_screenshot_3_url}/xxxlarge" data-lightbox="images" title="screenshot 3"><img src="{$item.market_screenshot_3_url}/size=xsmall/crop=auto" width="40" class="iloutline img_shadow"></a>
                    {/if}

                </div>
            </div>




            {elseif $type == 'Bundle'}

            <div class="col9">
                <div class="p5">
                    <h2>{$item.bundle_title}</h2><br>by <a href="{$item.profile_full_url}" target="_blank">@{$item.profile_url}</a>
                    <br><a href="{$item.bundle_detail_url}" target="_blank">more info...</a>
                    <br><br>
                    {if is_array($item.bundle_items)}
                    {assign var="not_all_installed" value="0"}
                    {assign var="allow_purchase" value="1"}

                    {foreach $item.bundle_items as $_i}

                        {if $_i@iteration == 9}
                            {assign var="add_close" value="1"}
                            <div id="b{$item._item_id}" style="width:100%;display:none">
                        {/if}

                        <div class="p5" style="display:table;width:100%">
                            <div style="display:table-row;width:100%">

                                <div style="display:table-cell;vertical-align:top;width:5%">
                                    <img src="{$_i.market_image_url}" width="64">
                                </div>

                                <div style="display:table-cell;padding:0 12px;vertical-align:top;text-align:left;width:95%">
                                    {if isset($_i.market_bundle_only) && $_i.market_bundle_only == 'on'}
                                        {assign var="price" value="<b>Only available in this bundle!</b>"}
                                    {elseif isset($_i.market_file_item_price) && $_i.market_file_item_price > 0}
                                        {assign var="price" value="&#36;{$_i.market_file_item_price|number_format:2}"}
                                    {else}
                                        {assign var="price" value="free"}
                                    {/if}

                                    {if is_dir("{$jamroom_dir}/{$_i.market_type}s/{$_i.market_name}")}

                                        <span class="status_section status_section_stable" style="height:14px;width:60px;display:inline-block;padding:2px;font-size:10px;margin-bottom:6px;">INSTALLED</span>&nbsp;&nbsp;<a href="{$_i.item_url}"><b>{$_i.item_title}</b></a> - {$price}
                                        {assign var="allow_purchase" value="0"}

                                    {elseif isset($_i.market_allow_license_install) && $_i.market_allow_license_install == '1'}

                                        {assign var="not_all_installed" value="1"}
                                        <span class="status_section" style="height:14px;width:60px;display:inline-block;padding:2px;font-size:10px">OWNED</span>&nbsp;&nbsp;<a href="{$_i.item_url}"><b>{$_i.item_title}</b></a> - {$price}

                                    {else}

                                        {assign var="not_all_installed" value="1"}
                                        <a href="{$_i.market_detail_url}" target="_blank"><b>{$_i.item_title}</b></a> - {$price}

                                    {/if}
                                    <br>
                                    {$_i.market_description|truncate:140}
                                </div>
                            </div>
                        </div>

                        {if $_i@iteration === 8 && $_i@total > 8}
                        <div class="p10" style="display:table;width:100%">
                            <div id="c{$item._item_id}" style="display:table-row;width:100%">
                                <div style="display:table-cell;width:100%">
                                    <a onclick="$('#c{$item._item_id}').hide(); $('#b{$item._item_id}').slideDown(300);">This Bundle includes <b>{math equation="x - y" x=$_i@total y=8} more items!</b> Click here to view.</a>
                                </div>
                            </div>
                        </div>
                        {/if}

                    {/foreach}
                    {if isset($add_close) && $add_close == "1"}
                        </div>
                        {assign var="add_close" value="0"}
                    {/if}
                    {/if}
                </div>
            </div>

            <div class="col3 last">
                <div class="p10" style="text-align:center;white-space:nowrap">

                    {* PAID Bundles *}
                    {if $item.bundle_item_price > 0}

                        <span style="display:inline-block;margin-bottom:10px">

                        {if $not_all_installed == '0'}

                            {* Everything is already installed from this bundle *}
                            <span style="display:inline-block;margin-bottom:10px"><h3>&#36;{$item.bundle_item_price|number_format:2}</h3></span><br>
                            <input type="button" class="form_button form_button_disabled" style="width:150px" value="Already Installed" onclick="window.location='{$jamroom_url}/{jrCore_module_url module=$item.market_name}/admin/info'">
                            {if isset($item.bundle_savings)}
                                <br><br><h3>Save &#36;{$item.bundle_savings|number_format:2}</h3>
                            {/if}

                        {elseif isset($quick_purchase_id) && strlen($quick_purchase_id) > 5 && isset($_conf.jrMarket_quick_purchase) && $_conf.jrMarket_quick_purchase == 'on'}

                            {jrMarket_purchase_button type="bundle" quick=true item=$item key=$api_key}

                        {else}

                            {jrMarket_purchase_button type="bundle" item=$item key=$api_key}

                        {/if}


                    {else}

                        {* FREE Bundle Items *}
                        <span style="display:inline-block;margin-bottom:10px"><h3>Free</h3></span><br>

                        {if $not_all_installed == '0'}

                            <input type="button" class="form_button form_button_disabled" style="width:150px" value="Already Installed" onclick="window.location='{$jamroom_url}/{jrCore_module_url module=$item.market_name}/admin/info'">

                        {else}

                            <img id="fsi_{$item._item_id}" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/submit.gif" width="24" height="24" style="display:none" alt="{jrCore_lang module="jrCore" id="73" default="working..."}">&nbsp;<input type="button" class="form_button" style="width:150px" value="install" onclick="if (confirm('Install all items in this bundle?')) { jrMarket_install_item('bundle','{$item._item_id}','{$item._item_id}'); }">

                        {/if}

                    {/if}

                </div>
            </div>

            {/if}

        </div>
    </div>
</div>
{/foreach}
{/if}

{* prev/next page profile footer links *}
{if $info.prev_page > 0 || $info.next_page > 0}
<table class="page_table">
    <tr class="nodrag nodrop">
        <td>
            <table class="page_table_pager">
                <tr>

                    <td class="page_table_pager_left">
                    {if $info.prev_page > 0}
                        <input type="button" value="{jrCore_lang module="jrCore" id=26 default="&lt;"}" class="form_button" onclick="window.location='{$browse_base_url}/p={$info.prev_page}'">
                    {/if}
                    </td>

                    <td nowrap="nowrap" class="page_table_pager_center">
                        <select name="p" class="page-table-jumper" onchange="var p=this.options[this.selectedIndex].value; jrCore_window_location('{$browse_base_url}/p='+ p)">
                        {foreach $pages as $pnum}
                            {if $pnum == $info.this_page}
                                <option value="{$pnum}" selected="selected"> {$pnum}</option>
                            {else}
                                <option value="{$pnum}"> {$pnum}</option>
                            {/if}
                        {/foreach}
                        </select> &nbsp;/ {$info.total_pages}
                    </td>

                    <td class="page_table_pager_right">
                    {if $info.next_page > 0}
                        <input type="button" value="{jrCore_lang module="jrCore" id=27 default="&gt;"}" class="form_button" onclick="window.location='{$browse_base_url}/p={$info.next_page}'">
                    {/if}
                    </td>

                </tr>
            </table>
        </td>
    </tr>
</table>
{/if}
