<?php
/**
 * Jamroom 5 System Core module
 *
 * copyright 2003 - 2015
 * by The Jamroom Network
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0.  Please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * The Jamroom Network
 * and are used under license - any licenses are included and
 * can be found in the "contrib" directory within this module.
 *
 * Jamroom may use modules and skins that are licensed by third party
 * developers, and licensed under a different license  - please
 * reference the individual module or skin license that is included
 * with your installation.
 *
 * This software is provided "as is" and any express or implied
 * warranties, including, but not limited to, the implied warranties
 * of merchantability and fitness for a particular purpose are
 * disclaimed.  In no event shall the Jamroom Network be liable for
 * any direct, indirect, incidental, special, exemplary or
 * consequential damages (including but not limited to, procurement
 * of substitute goods or services; loss of use, data or profits;
 * or business interruption) however caused and on any theory of
 * liability, whether in contract, strict liability, or tort
 * (including negligence or otherwise) arising from the use of this
 * software, even if advised of the possibility of such damage.
 * Some jurisdictions may not allow disclaimers of implied warranties
 * and certain statements in the above disclaimer may not apply to
 * you as regards implied warranties; the other terms and conditions
 * remain enforceable notwithstanding. In some jurisdictions it is
 * not permitted to limit liability and therefore such limitations
 * may not apply to you.
 *
 * @package View Functions
 * @copyright 2014 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrCore_show_activity_log
 * @param $_post array Posted parameters
 * @param $_user array Viewing User
 * @param $_conf array Global Config
 * @param $from string "dashboard" or empty
 * @return null
 */
function jrCore_show_activity_log($_post, $_user, $_conf, $from = '')
{
    $url = jrCore_get_module_url('jrCore');
    // construct our query
    $tbl = jrCore_db_table_name('jrCore', 'log');
    $tbd = jrCore_db_table_name('jrCore', 'log_debug');
    $req = "SELECT * FROM {$tbl} l LEFT JOIN {$tbd} d ON d.log_log_id = l.log_id ";
    if (isset($_post['eo']) && $_post['eo'] == '1') {
        $req .= "WHERE log_priority != 'inf' ";
        $mod = 'AND';
        $num = null;
    }
    else {
        $mod = 'WHERE';
        $num = jrCore_db_number_rows('jrCore', 'log');
    }
    $_ex = false;
    $add = '';
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $_post['search_string'] = trim(urldecode($_post['search_string']));
        $str                    = jrCore_db_escape($_post['search_string']);
        $req .= "{$mod} (l.log_text LIKE '%{$str}%' OR l.log_ip LIKE '%{$str}%' OR l.log_priority LIKE '%{$str}%') ";
        $_ex = array('search_string' => $_post['search_string']);
        $add = '/search_string=' . urlencode($_post['search_string']);
        $num = false;
    }
    $req .= 'ORDER BY l.log_id DESC';

    // find how many lines we are showing
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }
    $_rt = jrCore_db_paged_query($req, $_post['p'], 12, 'NUMERIC', $num);

    $bu = "{$_conf['jrCore_base_url']}/{$url}/activity_log";
    if ($from && $from == 'dashboard') {
        $bu = "{$_conf['jrCore_base_url']}/{$url}/dashboard/activity";
    }

    // start our html output
    $eo = '';
    if (isset($_post['eo']) && $_post['eo'] == '1') {
        $buttons = jrCore_page_button('eo', 'all entries', "jrCore_window_location('{$bu}')");
        $eo      = '/eo=1';
    }
    else {
        $buttons = jrCore_page_button('eo', 'errors only', "jrCore_window_location('{$bu}/eo=1')");
    }
    $buttons .= jrCore_page_button('download', 'download', "if(confirm('Do you want to download the activity log as a CSV file?')){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/activity_log_download') }");
    if (jrUser_is_master()) {
        $buttons .= jrCore_page_button('delete', 'empty', "if(confirm('Delete all activity log entries?')){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/activity_log_delete_all') }");
    }
    jrCore_page_banner('activity log', $buttons);
    jrCore_get_form_notice();
    jrCore_page_search('search', "{$bu}{$eo}");

    $dat = array();
    if (jrUser_is_master()) {
        $dat[1]['title'] = '&nbsp;';
        $dat[1]['width'] = '2%;';
        $dat[2]['title'] = 'date';
        $dat[2]['width'] = '4%;';
    }
    else {
        $dat[2]['title'] = 'date';
        $dat[2]['width'] = '6%;';
    }
    $dat[3]['title'] = 'IP';
    $dat[3]['width'] = '5%;';
    $dat[4]['title'] = 'text';
    $dat[4]['width'] = '87%;';
    $dat[5]['title'] = '&nbsp;';
    $dat[5]['width'] = '2%;';
    jrCore_page_table_header($dat);
    unset($dat);

    if (isset($_rt['_items']) && is_array($_rt['_items'])) {

        // LOG LINE
        foreach ($_rt['_items'] as $k => $_log) {

            $dat = array();
            if (jrUser_is_master()) {
                $dat[1]['title'] = jrCore_page_button("d{$k}", 'X', " jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/activity_log_delete/id={$_log['log_id']}/p={$_post['p']}{$add}')");
            }
            $dat[2]['title'] = jrCore_format_time($_log['log_created']);
            $dat[2]['class'] = 'center nowrap';
            $dat[3]['title'] = $_log['log_ip'];
            if (isset($_post['search_string']{0})) {
                $dat[4]['title'] = jrCore_hilight_string($_log['log_text'], $_post['search_string']);
            }
            else {
                $dat[4]['title'] = $_log['log_text'];
            }
            $dat[4]['class'] = "log-{$_log['log_priority']}";
            if (isset($_log['log_data']{1})) {
                $dat[5]['title'] = jrCore_page_button("r{$k}", ' ! ', "popwin('{$_conf['jrCore_base_url']}/{$_post['module_url']}/log_debug/{$_log['log_id']}','debug',900,600,'yes');");
            }
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt, $_ex);
    }
    else {
        $dat = array();
        if (!empty($_post['search_string'])) {
            $dat[1]['title'] = '<p>There were no Activity Logs found to match your search criteria</p>';
        }
        else {
            $dat[1]['title'] = '<p>There does not appear to be any Activity Logs</p>';
        }
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
}

/**
 * Show the Skin Style Editor
 * @param $skin string Skin name we are editing
 * @param $_post array Posted values
 * @param $_user array User array
 * @param $_conf array Global Config array
 * @return mixed
 */
function jrCore_show_skin_style($skin, $_post, $_user, $_conf)
{
    global $_mods;
    jrCore_page_skin_tabs($skin, 'style');

    // What are our available tab options?
    $_op = array(
        'simple'   => 'Color and Font',
        'padding'  => 'Padding and Margin',
        'advanced' => 'Advanced',
        'extra'    => 'Untagged'
    );

    // What CSS Rules are aligned with each option?
    // NOTE: "advanced" is the default if a CSS rule is NOT defined here
    // Define the CSS params that are SIMPLE CSS params
    $_or = array(
        'simple'  => array(
            'background-color' => 1,
            'color'            => 1,
            'font-family'      => 1,
            'font-size'        => 1,
            'font-weight'      => 1,
            'text-transform'   => 1
        ),
        'padding' => array(
            'padding'        => 1,
            'padding-top'    => 1,
            'padding-right'  => 1,
            'padding-bottom' => 1,
            'padding-left'   => 1,
            'margin'         => 1,
            'margin-top'     => 1,
            'margin-right'   => 1,
            'margin-bottom'  => 1,
            'margin-left'    => 1
        ),
    );

    // Default to simple section
    if (!isset($_post['section']) || strlen($_post['section']) === 0) {
        $section = 'advanced';
    }
    else {
        $section = $_post['section'];
    }

    // Get files
    $_files = glob(APP_DIR . "/skins/{$skin}/css/*.css");
    if (!$_files || !is_array($_files)) {
        jrCore_notice_page('error', 'There do not appear to be any CSS files for this skin!');
        return false;
    }
    $_md = jrCore_skin_meta_data($skin);
    $ttl = (isset($_md['title'])) ? $_md['title'] : $skin;
    $_fl = array(
        $ttl => array()
    );
    foreach ($_files as $full_file) {
        $tmp = file_get_contents($full_file);
        if ($section == 'extra' || strpos($tmp, '@title')) {
            $nam             = basename($full_file);
            $_fl[$ttl][$nam] = $nam;
        }
    }

    // We also need to add in any module CSS files so they can be tweaked
    $_tm = jrCore_get_registered_module_features('jrCore', 'css');
    if ($_tm) {
        foreach ($_tm as $mod => $_v) {
            foreach ($_v as $full_file => $ignore) {
                if (!strpos($full_file, '/')) {
                    $full_file = APP_DIR . "/modules/{$mod}/css/{$full_file}";
                }
                if (!is_file($full_file)) {
                    // file no longer exists
                    continue;
                }
                if ($section == 'extra' || strpos(file_get_contents($full_file), '@title')) {
                    $nam             = basename($full_file);
                    $ttl             = $_mods[$mod]['module_name'];
                    $_fl[$ttl][$nam] = $nam;
                    $_files[]        = $full_file;
                }
            }
        }
    }

    // See if we were given a selector
    $found = false;
    $ffile = array();
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        foreach ($_files as $full_file) {
            $tmp = file_get_contents($full_file);
            if (strpos($tmp, '@title') || $section != 'simple') {
                $_cs = jrCore_parse_css_file($full_file, $section);
                if ($_cs && is_array($_cs) && count($_cs) > 0) {
                    foreach ($_cs as $rule => $opts) {
                        if ($rule == $_post['search_string'] || $rule == ".{$_post['search_string']}" || $rule == "#{$_post['search_string']}" || strpos($rule, "{$_post['search_string']} ") === 0 || strpos($rule, ".{$_post['search_string']} ") === 0 || strpos($rule, "#{$_post['search_string']} ") === 0 || (isset($opts['title']) && stripos(' ' . $opts['title'], $_post['search_string']))) {
                            if (!$found) {
                                $found = array();
                            }
                            $found[$rule] = $opts;
                            $ffile[$rule] = basename($full_file);
                        }
                    }
                }
            }
        }
        if ($found && is_array($found)) {
            $_op['search']    = 'Search Results';
            $_post['section'] = 'search';
            $section          = 'search';
        }
    }

    $url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/style/skin={$_post['skin']}";
    if (isset($_post['file']) && strlen($_post['file']) > 2) {
        $url .= '/file=' . jrCore_entity_string($_post['file']);
    }
    $_tb = array();
    foreach ($_op as $tab => $title) {
        $_tb[$tab] = array(
            'label' => $title,
            'url'   => "{$url}/section={$tab}"
        );
    }

    $_tb[$section]['active'] = true;
    jrCore_page_tab_bar($_tb);

    $url      = jrCore_get_module_url('jrCore');
    $subtitle = '<select name="skin_jumper" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $_conf['jrCore_base_url'] . "/{$url}/skin_admin/style/skin=' + $(this).val() + '/section=" . $section . "')\">";
    $_tmpm    = jrCore_get_skins();
    foreach ($_tmpm as $skin_dir => $_skin) {
        $_mta = jrCore_skin_meta_data($skin_dir);
        $name = (isset($_mta['title'])) ? $_mta['title'] : $skin_dir;
        if ($skin_dir == $_post['skin']) {
            $subtitle .= '<option value="' . $_post['skin'] . '" selected="selected"> ' . $name . "</option>\n";
        }
        else {
            $subtitle .= '<option value="' . $skin_dir . '"> ' . $name . "</option>\n";
        }
    }
    $subtitle .= '</select>';
    jrCore_page_banner('Style Editor', $subtitle);
    jrCore_get_form_notice();

    $ssubm = false;
    if (isset($_post['search_string']) && !$found) {
        $ssubm = true;
    }

    // See if we have been given a file to edit - if not, use first in list
    if (!isset($_post['file']{0})) {
        $_post['file'] = reset($_fl);
        $_post['file'] = basename(reset($_post['file']));
    }

    $full_file = APP_DIR . "/skins/{$skin}/css/{$_post['file']}";
    if (!is_file($full_file) && $_tm) {
        // See if this is a module CSS file...
        foreach ($_tm as $mod => $_v) {
            foreach ($_v as $ff => $ignore) {
                if ($ff == $_post['file']) {
                    $full_file = APP_DIR . "/modules/{$mod}/css/{$ff}";
                    break 2;
                }
            }
        }
    }
    if (!is_file($full_file)) {
        jrCore_notice_page('error', 'Unable to open CSS file - please try again');
        return false;
    }

    if (!$found) {
        $_tmp = jrCore_parse_css_file($full_file, $section);
    }
    else {
        $_tmp = $found;
    }
    if ($section != 'advanced' && $_tmp && is_array($_tmp)) {
        foreach ($_tmp as $name => $_inf) {
            $frl = false;
            if (isset($_inf['rules']) && is_array($_inf['rules'])) {
                foreach ($_inf['rules'] as $rule => $val) {
                    if (isset($_or[$section]) && !isset($_or[$section][$rule])) {
                        continue;
                    }
                    $frl = true;
                }
            }
            if (!$frl) {
                unset($_tmp[$name]);
            }
        }
    }

    // Now we have the "base" CSS - we next need to load in the customizations
    // from the database if they have any
    $tbl = jrCore_db_table_name('jrCore', 'skin');
    $req = "SELECT skin_custom_css FROM {$tbl} WHERE skin_directory = '" . jrCore_db_escape($skin) . "'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    $_cr = array();
    if ($_rt && is_array($_rt) && strlen($_rt['skin_custom_css']) > 3) {
        $_new = json_decode($_rt['skin_custom_css'], true);
        $_rep = array('#', '"', "'", 'px', '%', 'em');
        if ($_new && is_array($_new)) {
            foreach ($_new as $cname => $_cinf) {
                if (isset($_tmp[$cname])) {
                    // See what has changed
                    foreach ($_cinf as $r => $t) {
                        if (isset($_tmp[$cname]['rules'][$r])) {
                            $one = trim(str_replace($_rep, '', $_tmp[$cname]['rules'][$r]));
                            $two = trim(str_replace($_rep, '', $t));
                            if ($one != $two) {
                                if (!isset($_cr[$cname])) {
                                    $_cr[$cname] = array();
                                }
                                $_cr[$cname][$r] = 1;
                            }
                        }
                    }
                    $_tmp[$cname]['rules'] = array_merge($_tmp[$cname]['rules'], $_cinf);
                }
            }
        }
    }

    $subm = true;
    if (!$_tmp || !is_array($_tmp) || count($_tmp) === 0) {
        $subm = false;
    }
    elseif (!$ssubm) {

        // Form init
        $_fld = array(
            'submit_value' => 'save changes',
            'action'       => "skin_admin_save/style/skin={$skin}/section={$section}"
        );
        jrCore_form_create($_fld);

        $_fld = array(
            'name'  => 'file',
            'type'  => 'hidden',
            'value' => $_post['file']
        );
        jrCore_form_field_create($_fld);
    }

    // Style Jumper...
    if (isset($_fl) && is_array($_fl) && count($_fl) > 1) {
        if ($section != 'search') {
            // Make sure $_fl contains our file...
            $fnf = false;
            foreach ($_fl as $m => $_f) {
                if (isset($_f["{$_post['file']}"])) {
                    $fnf = true;
                    break;
                }
            }
            if (!$fnf) {
                $_fl["{$_post['file']}"] = $_post['file'];
            }
            $_fld = array(
                'name'     => 'file',
                'label'    => 'style section',
                'type'     => 'select',
                'options'  => $_fl,
                'value'    => $_post['file'],
                'onchange' => "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/style/skin={$skin}/file='+ $(this).val() + '/section={$section}')"
            );
            jrCore_form_field_create($_fld);
        }
        $val = null;
        if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
            if ($found) {
                $val = jrCore_entity_string($_post['search_string']);
                jrCore_set_form_notice('success', "Showing title, element, class, and ID matches for: <strong>{$val}</strong>", false);
                jrCore_get_form_notice();
            }
            else {
                $_tmp = array();
            }
        }
        jrCore_page_search('selector search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/style/skin={$_post['skin']}/file={$_post['file']}/section={$section}", null, false);
        jrCore_page_divider();
    }

    if (!$subm || $ssubm) {
        if ($ssubm) {
            jrCore_set_form_notice('error', 'There were no CSS selectors found to match your search');
        }
        else {
            jrCore_set_form_notice('error', 'There are no CSS Rules of this type found in this section');
        }
        jrCore_get_form_notice();
    }

    $color_opts = '<option value="transparent">transparent</option>';
    // Generate web safe colors
    $cs = array('00', '33', '66', '99', 'CC', 'FF');
    for ($i = 0; $i < 6; $i++) {
        for ($j = 0; $j < 6; $j++) {
            for ($k = 0; $k < 6; $k++) {
                $c = $cs[$i] . $cs[$j] . $cs[$k];
                $color_opts .= "<option value=\"{$c}\">#{$c}</option>\n";
            }
        }
    }

    // Padding/margins
    $_pixels = array('auto' => 'auto');
    foreach (range(0, 50) as $pix) {
        $_pixels["{$pix}px"] = "{$pix}px";
    }

    // Width/Height
    $_width_perc = array();
    foreach (range(1, 100) as $pix) {
        $_width_perc["{$pix}%"] = "{$pix}%";
    }

    $_width_pix = array();
    foreach (range(10, 600, 5) as $pix) {
        $_width_pix["{$pix}px"] = "{$pix}px";
    }

    $_css_opts = array();

    // Our fonts
    $_css_opts['font-family'] = array(
        'Arial'                => 'Arial',
        'Arial Black'          => 'Arial Black',
        'Courier New'          => 'Courier New',
        'Georgia'              => 'Georgia',
        'Impact'               => 'Impact',
        'monospace'            => 'monospace',
        'Times New Roman'      => 'Times New Roman',
        'Trebuchet MS'         => 'Trebuchet MS',
        'Verdana'              => 'Verdana',
        'MS Sans Serif,Geneva' => 'sans-serif'
    );

    // Our sizes
    $_css_opts['font-size'] = array();
    foreach (range(8, 96) as $pix) {
        $_css_opts['font-size']["{$pix}px"] = "{$pix}px";
    }

    // Weights
    $_css_opts['font-weight'] = array(
        'normal'  => 'normal',
        'bold'    => 'bold',
        'bolder'  => 'bolder',
        'lighter' => 'lighter',
        'inherit' => 'inherit'
    );

    // Style
    $_css_opts['font-style'] = array(
        'normal' => 'normal',
        'italic' => 'italic'
    );

    // Variant
    $_css_opts['font-variant'] = array(
        'normal'     => 'normal',
        'small-caps' => 'small-caps'
    );

    // Text-Transform
    $_css_opts['text-transform'] = array(
        'none'       => 'none',
        'capitalize' => 'capitalize',
        'uppercase'  => 'uppercase',
        'lowercase'  => 'lowercase',
        'inherit'    => 'inherit'
    );

    // Text-Align
    $_css_opts['text-align'] = array(
        'left'    => 'left',
        'right'   => 'right',
        'center'  => 'center',
        'justify' => 'justify',
        'inherit' => 'inherit'
    );

    // Text-Decoration
    $_css_opts['text-decoration'] = array(
        'none'         => 'none',
        'underline'    => 'underline',
        'overline'     => 'overline',
        'line-through' => 'line-through',
        'blink'        => 'blink',
        'inherit'      => 'inherit'
    );

    // Opacity
    $_css_opts['opacity'] = array(
        '0.05' => '0.05',
        '0.1'  => '0.1',
        '0.15' => '0.15',
        '0.2'  => '0.2',
        '0.25' => '0.25',
        '0.3'  => '0.3',
        '0.35' => '0.35',
        '0.4'  => '0.4',
        '0.45' => '0.45',
        '0.5'  => '0.5',
        '0.55' => '0.55',
        '0.6'  => '0.6',
        '0.65' => '0.65',
        '0.7'  => '0.7',
        '0.75' => '0.75',
        '0.8'  => '0.8',
        '0.85' => '0.85',
        '0.9'  => '0.9',
        '0.95' => '0.95',
        '1.0'  => '1.0'
    );

    // $_tmp will now contain what we are editing
    if (isset($_tmp) && is_array($_tmp)) {

        $r_id = 0;
        $key  = false;
        foreach ($_tmp as $name => $_inf) {

            if ($found && is_array($found) && !isset($found[$name])) {
                continue;
            }
            // Process each rule...

            $_out = array();
            if (isset($_inf['rules']) && is_array($_inf['rules'])) {
                foreach ($_inf['rules'] as $rule => $val) {

                    // Check for multiple value rules..
                    if (substr_count(strtolower($val), 'px') > 1) {
                        continue;
                    }

                    $val = str_replace(array('"', "'"), '', $val);

                    // Pass this in as a hidden form field so we can line them back up on submission
                    $key = 'jrse' . ++$r_id;
                    if (stripos($val, '!important')) {
                        // We don't deal with !important here
                        $val = trim(str_ireplace('!important', '', $val));
                        $hid = '<input type="hidden" name="' . $key . '_s" value="' . $name . '~' . $rule . '"><input type="hidden" name="' . $key . '_add_important" value="on">';
                    }
                    else {
                        $hid = '<input type="hidden" name="' . $key . '_s" value="' . $name . '~' . $rule . '">';
                    }

                    if ($section != 'advanced' && isset($_or[$section]) && !isset($_or[$section][$rule])) {
                        continue;
                    }

                    // Our tag is used to let the user know what they are changing
                    $tag = $rule;

                    // See what we are doing
                    switch ($rule) {

                        //------------------------
                        // opacity
                        //------------------------
                        case 'opacity':
                            $opts = array();
                            foreach ($_css_opts[$rule] as $fcss => $fname) {
                                if (isset($fcss) && $fcss == $val) {
                                    $opts[] = '<option selected="selected" value="' . $fcss . '">' . $fname . '</option>';
                                }
                                else {
                                    $opts[] = '<option value="' . $fcss . '">' . $fname . '</option>';
                                }
                            }
                            $_out[] = $hid . '<p class="style-label">' . $rule . '</p><select id="' . $key . '" name="' . $key . '" class="style-select">' . implode("\n", $opts) . '</select>';
                            break;

                        //------------------------
                        // background-color
                        //------------------------
                        case 'color':
                            $tag = 'font-color';
                        case 'border-color':
                        case 'border-top-color':
                        case 'border-right-color':
                        case 'border-bottom-color':
                        case 'border-left-color':
                        case 'background-color':
                            // Show color selector
                            if ($val == 'transparent') {
                                $color_opts .= "<option value=\"" . str_replace('#', '', $val) . "\" selected=\"selected\">{$val}</option>";
                            }
                            else {
                                $color_opts .= "<option value=\"" . strtoupper(str_replace('#', '', $val)) . "\" selected=\"selected\">{$val}</option>";
                            }
                            $_out[] = $hid . '<p class="style-label">' . $tag . '</p><select id="' . $key . '" name="' . $key . '" class="style-select">' . $color_opts . '</select>';
                            $_tmp   = jrCore_get_flag('style_color_picker');
                            if (!$_tmp) {
                                $_tmp = array();
                            }
                            $_tmp[] = array('$(\'#' . $key . '\').colourPicker();');
                            jrCore_set_flag('style_color_picker', $_tmp);
                            break;

                        //------------------------
                        // fonts
                        //------------------------
                        case 'font-family':
                            // Our "current" selection could be a compound font family - i.e.
                            // Open Sans,Tahoma,sans-serif
                            // in this case we need to make sure it is a choice in our $_css_opts
                            if (strpos($val, ',')) {
                                $_css_opts['font-family'][$val] = $val;
                            }
                        case 'font-size':
                        case 'font-weight':
                        case 'font-style':
                        case 'font-variant':
                        case 'text-transform':
                        case 'text-align':
                        case 'text-decoration':
                            $opts = array();
                            foreach ($_css_opts[$rule] as $fcss => $fname) {
                                switch ($rule) {
                                    case 'font-family':
                                        $style = ' style="font-family:' . $fcss . '"';
                                        break;
                                    default:
                                        $style = '';
                                        break;
                                }
                                if (isset($fcss) && $fcss == $val) {
                                    $opts[] = '<option value="' . $fcss . '"' . $style . ' selected="selected">' . $fname . '</option>';
                                }
                                else {
                                    $opts[] = '<option value="' . $fcss . '"' . $style . '>' . $fname . '</option>';
                                }
                            }
                            // Show font family select
                            $rtag = $rule;
                            if (isset($_cr[$name][$rule])) {
                                $rtag = '<i>' . $rule . '</i>';
                            }
                            $_out[] = "\n" . $hid . '<p class="style-label">' . $rtag . '</p><select id="' . $key . '" name="' . $key . '" class="style-select">' . implode("\n", $opts) . '</select>';
                            break;

                        //------------------------
                        // border-style
                        //------------------------
                        case 'border-style':
                        case 'border-top-style':
                        case 'border-right-style':
                        case 'border-bottom-style':
                        case 'border-left-style':
                            $opts = array();
                            $_brd = array('none', 'dotted', 'dashed', 'solid', 'double', 'groove', 'ridge', 'inset', 'outset');
                            foreach ($_brd as $v) {
                                if (isset($v) && $v == $val) {
                                    $opts[] = '<option selected="selected" value="' . $v . '">' . $v . '</option>';
                                }
                                else {
                                    $opts[] = '<option value="' . $v . '">' . $v . '</option>';
                                }
                            }
                            // Show select
                            $rtag = $rule;
                            if (isset($_cr[$name][$rule])) {
                                $rtag = '<i>' . $rule . '</i>';
                            }
                            $_out[] = $hid . '<p class="style-label">' . $rtag . '</p><select id="' . $key . '" name="' . $key . '" class="style-select">' . implode("\n", $opts) . '</select>';
                            break;

                        //------------------------
                        // padding/margin/border
                        //------------------------
                        case 'border-width':
                        case 'border-top-width':
                        case 'border-right-width':
                        case 'border-bottom-width':
                        case 'border-left-width':
                        case 'border-radius':
                        case 'border-top-left-radius':
                        case 'border-top-right-radius':
                        case 'border-bottom-left-radius':
                        case 'border-bottom-right-radius':
                        case 'padding':
                        case 'padding-top':
                        case 'padding-bottom':
                        case 'padding-left':
                        case 'padding-right':
                        case 'margin':
                        case 'margin-top':
                        case 'margin-bottom':
                        case 'margin-left':
                        case 'margin-right':
                        case 'top':
                        case 'right':
                        case 'left':
                        case 'bottom':
                        case 'line-height':
                            // See if we need to INCREASE our size-array
                            if (!isset($_pixels[$val])) {

                                // See if this is a double value - i.e. "0 auto"
                                if (stripos($val, 'auto') && strpos($val, ' ')) {
                                    $hid = '<input type="hidden" name="' . $key . '_s" value="' . $name . '~' . $rule . '"><input type="hidden" name="' . $key . '_add_auto" value="on">';
                                    $val = substr($val, 0, strpos($val, ' '));
                                }

                                // Make sure the value we are set AT is selected - even if not in array
                                $tmp_val = intval($val);
                                if (jrCore_checktype($tmp_val, 'number_nz') && $tmp_val > 50) {
                                    foreach (range(51, $tmp_val) as $tnum) {
                                        $_pixels["{$tnum}px"] = "{$tnum}px";
                                    }
                                    foreach (range(($tmp_val + 1), ($tmp_val + 25)) as $tnum) {
                                        $_pixels["{$tnum}px"] = "{$tnum}px";
                                    }
                                    natcasesort($_pixels);
                                }

                            }
                            $opts = array();
                            foreach ($_pixels as $size) {
                                if (isset($size) && $size == $val) {
                                    $opts[] = '<option selected="selected" value="' . $size . '">' . $size . '</option>';
                                }
                                else {
                                    $opts[] = '<option value="' . $size . '">' . $size . '</option>';
                                }
                            }
                            $rtag = $rule;
                            if (isset($_cr[$name][$rule])) {
                                $rtag = '<i>' . $rule . '</i>';
                            }
                            $_out[] = $hid . '<p class="style-label">' . $rtag . '</p><select id="' . $key . '" name="' . $key . '" class="style-select">' . implode("\n", $opts) . '</select>';
                            break;

                        case 'width':
                        case 'height':
                        case 'min-width';
                        case 'min-height';
                            $opts = array();
                            if (strpos($val, '%')) {
                                if (!in_array($val, $_width_perc)) {
                                    $_width_perc[] = $val;
                                    sort($_width_perc, SORT_NUMERIC);
                                }
                                foreach ($_width_perc as $size) {
                                    if (isset($size) && $size == $val) {
                                        $opts[] = '<option selected="selected" value="' . $size . '">' . $size . '</option>';
                                    }
                                    else {
                                        $opts[] = '<option value="' . $size . '">' . $size . '</option>';
                                    }
                                }
                            }
                            else {
                                // Make sure the value we HAVE is always set
                                if (!in_array($val, $_width_pix)) {
                                    $_width_pix[] = $val;
                                    sort($_width_pix, SORT_NUMERIC);
                                }
                                foreach ($_width_pix as $size) {
                                    if (isset($size) && $size == $val) {
                                        $opts[] = '<option selected="selected" value="' . $size . '">' . $size . '</option>';
                                    }
                                    else {
                                        $opts[] = '<option value="' . $size . '">' . $size . '</option>';
                                    }
                                }
                            }
                            $rtag = $rule;
                            if (isset($_cr[$name][$rule])) {
                                $rtag = '<i>' . $rule . '</i>';
                            }
                            $_out[] = $hid . '<p class="style-label">' . $rtag . '</p><select id="' . $key . '" name="' . $key . '" class="style-select">' . implode("\n", $opts) . '</select>';
                            break;
                    }
                }
            }
            if (isset($_out) && is_array($_out) && count($_out) > 0) {
                $rst = '';
                if (isset($_cr[$name])) {
                    // We had some customizations in this element
                    $rst = '<div class="style-reset">' . jrCore_page_button("r{$key}", 'reset', "if (confirm('Are you sure you want to reset this element to the default?')){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/css_reset_save/skin={$skin}/tag=" . urlencode($name) . "')}") . '</div>';
                }
                $_field = array(
                    'name'  => $key,
                    'type'  => 'custom',
                    'html'  => '<div class="style-box">' . implode('<br>', $_out) . '</div>' . $rst,
                    'label' => $_inf['title'],
                    'help'  => $_inf['help']
                );
                if ($name != $_inf['title']) {
                    $_field['sublabel'] = $name;
                }
                else {
                    $_field['label'] = '<span style="text-transform:none">' . $_field['label'] . '</span>';
                }
                if (isset($ffile[$name])) {
                    if (!isset($_field['sublabel'])) {
                        $_field['sublabel'] = '';
                    }
                    $_field['sublabel'] .= '<br>file: ' . $ffile[$name];
                }
                jrCore_form_field_create($_field);
            }
        }
    }
    jrCore_page_set_no_header_or_footer();
    return jrCore_page_display(true);
}

/**
 * Image replacer and customization form
 * @param $type string Type module|image
 * @param $skin string Skin name
 * @param $_post array Post data
 * @param $_user array User info
 * @param $_conf array Global Config
 * @return mixed
 */
function jrCore_show_skin_images($type, $skin, $_post, $_user, $_conf)
{
    global $_mods;
    // Generate our output
    if (isset($type) && $type == 'module') {
        jrCore_page_admin_tabs($skin, 'images');
        $action = "admin_save/images/module={$skin}";
    }
    else {
        jrCore_page_skin_tabs($skin, 'images');
        $action = "skin_admin_save/images/skin={$skin}";
    }

    if ($type == 'module') {
        // Setup our module jumper
        $subtitle = '<select name="mod_select" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $_conf['jrCore_base_url'] . "/'+ $(this).val() +'/admin/images')\">";
        $_tmpm    = array();
        foreach ($_mods as $mod_dir => $_info) {
            $_tmpm[$mod_dir] = $_info['module_name'];
        }
        asort($_tmpm);
        foreach ($_tmpm as $mod_dir => $title) {
            if (!jrCore_module_is_active($mod_dir)) {
                continue;
            }
            if (is_dir(APP_DIR . "/modules/{$mod_dir}/img")) {
                if ($mod_dir == $_post['module']) {
                    $subtitle .= '<option value="' . $_post['module_url'] . '" selected="selected"> ' . $title . "</option>\n";
                }
                else {
                    $murl = jrCore_get_module_url($mod_dir);
                    $subtitle .= '<option value="' . $murl . '"> ' . $title . "</option>\n";
                }
            }
        }
    }
    else {
        $url      = jrCore_get_module_url('jrCore');
        $subtitle = '<select name="skin_jumper" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $_conf['jrCore_base_url'] . "/{$url}/skin_admin/images/skin='+ $(this).val())\">";
        $_tmpm    = jrCore_get_skins();
        foreach ($_tmpm as $skin_dir => $_skin) {
            if (is_dir(APP_DIR . "/skins/{$skin_dir}/img")) {
                $_mta = jrCore_skin_meta_data($skin_dir);
                $name = (isset($_mta['title'])) ? $_mta['title'] : $skin_dir;
                if ($skin_dir == $_post['skin']) {
                    $subtitle .= '<option value="' . $_post['skin'] . '" selected="selected"> ' . $name . "</option>\n";
                }
                else {
                    $subtitle .= '<option value="' . $skin_dir . '"> ' . $name . "</option>\n";
                }
            }
        }
    }
    $subtitle .= '</select>';

    jrCore_page_banner('Images', $subtitle);
    // See if we are disabled
    if (!jrCore_module_is_active($_post['module'])) {
        jrCore_set_form_notice('notice', 'This module is currently disabled');
    }
    jrCore_get_form_notice();

    if (!isset($_conf["jrCore_{$skin}_custom_images"])) {
        // Custom image container (per skin)
        $_tmp = array(
            'name'     => "{$skin}_custom_images",
            'default'  => '',
            'type'     => 'hidden',
            'required' => 'on',
            'validate' => 'false',
            'label'    => "{$skin} custom images",
            'help'     => 'this hidden field holds the names of images that have been customized'
        );
        jrCore_register_setting('jrCore', $_tmp);
    }

    // Form init
    $_tmp = array(
        'submit_value'     => 'save changes',
        'action'           => $action,
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    $dat             = array();
    $dat[1]['title'] = 'default';
    $dat[1]['width'] = '30%';
    $dat[2]['title'] = 'active';
    $dat[2]['width'] = '5%';
    $dat[3]['title'] = 'custom';
    $dat[3]['width'] = '30%';
    $dat[4]['title'] = 'upload custom';
    $dat[4]['width'] = '35%';
    jrCore_page_table_header($dat);

    // Get any custom images
    $_cust = (isset($_conf["jrCore_{$skin}_custom_images"]{2})) ? json_decode($_conf["jrCore_{$skin}_custom_images"], true) : array();

    // Get all of our actual template files...
    // See if we are doing a module or a skin...
    if (isset($type) && $type == 'module') {
        $t_url = 'modules';
        $t_tag = 'mod_';
        $_imgs = glob(APP_DIR . "/modules/{$skin}/img/*.{png,jpg,gif,ico}", GLOB_BRACE);
        $u_tag = 'mod';
    }
    else {
        $t_url = 'skins';
        $t_tag = '';
        $_imgs = glob(APP_DIR . "/skins/{$skin}/img/*.{png,jpg,gif,ico}", GLOB_BRACE);
        $u_tag = 'skin';
    }
    $curl = jrCore_get_module_url('jrCore');

    if (isset($_imgs) && is_array($_imgs)) {
        foreach ($_imgs as $k => $full_file) {
            $dat = array();
            $img = basename($full_file);
            if (strpos($img, 'screenshot') === 0) {
                continue;
            }
            $_is = getimagesize($full_file);
            $url = "{$_conf['jrCore_base_url']}/{$t_url}/{$skin}/img/{$img}";

            $w = $_is[0];
            $h = $_is[1];
            $l = false;
            if (isset($h) && $h > 100) {
                $w = (($w / $h) * 100);
                $h = 100;
                $l = true;
                // See if our width is greater than 100 here...
                if (isset($w) && $w > 100) {
                    $h = (($h / $w) * 100);
                    $w = 100;
                }
            }
            elseif (isset($w) && $w > 100) {
                $h = (($h / $w) * 100);
                $w = 100;
                $l = true;
            }
            if ($l) {
                $dat[1]['title'] = "<a href=\"{$url}\" data-lightbox=\"images\" title=\"{$img}\"><img src=\"{$url}?r=" . mt_rand() . "\" height=\"{$h}\" width=\"{$w}\" alt=\"{$img}\" title=\"{$img}\"></a>";
            }
            else {
                $dat[1]['title'] = "<img src=\"{$url}?r=" . mt_rand() . "\" height=\"{$h}\" width=\"{$w}\" alt=\"{$img}\" title=\"{$img}\">";
            }
            $dat[1]['class'] = 'center';

            if (isset($_cust[$img])) {
                $chk = '';
                if (isset($_cust[$img][1]) && $_cust[$img][1] == 'on') {
                    $chk = ' checked="checked"';
                }
                $dat[2]['title'] = '<input type="hidden" name="name_' . $k . '_active" value="off"><input type="checkbox" name="name_' . $k . '_active" class="form-checkbox"' . $chk . '>';
                $dat[2]['class'] = 'center';
            }
            else {
                $dat[2]['title'] = '&nbsp;';
            }

            if (isset($_cust[$img])) {
                // We have a custom image
                $url = "{$_conf['jrCore_base_url']}/data/media/0/0/{$t_tag}{$skin}_{$img}";
                $_is = getimagesize(APP_DIR . "/data/media/0/0/{$t_tag}{$skin}_{$img}");

                $w = $_is[0];
                $h = $_is[1];
                $l = false;
                if (isset($h) && $h > 100) {
                    $w = (($w / $h) * 100);
                    $h = 100;
                    $l = true;
                    // See if our width is greater than 100 here...
                    if (isset($w) && $w > 100) {
                        $w = 100;
                    }
                }
                elseif (isset($w) && $w > 100) {
                    $h = (($h / $w) * 100);
                    $w = 100;
                    $l = true;
                }
                $dat[3]['title'] = '<div style="width:120px;display:inline-block">';
                if ($l) {
                    $dat[3]['title'] .= "<a href=\"{$url}\" data-lightbox=\"images\" title=\"{$img}\"><img src=\"{$url}?r=" . mt_rand() . "\" height=\"{$h}\" width=\"{$w}\" alt=\"{$img}\" title=\"{$img}\"></a>";
                }
                else {
                    $dat[3]['title'] .= "<img src=\"{$url}?r=" . mt_rand() . "\" height=\"{$h}\" width=\"{$w}\" height=\"{$_is[1]}\" alt=\"{$img}\" title=\"{$img}\">";
                }
                $dat[3]['title'] .= "</div>&nbsp;" . jrCore_page_button("d{$k}", 'delete', "if (confirm('Are you sure you want to delete this custom image?')){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$curl}/skin_image_delete_save/{$u_tag}={$skin}/name={$img}')}");
                unset($_cust[$img]);
            }
            else {
                $dat[3]['title'] = '&nbsp;';
            }
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = '<input type="hidden" name="name_' . $k . '" value="' . $img . '"><input type="file" name="file_' . $k . '"><br><span class="sublabel"><strong>' . $img . '</strong> - <strong>' . $_is[0] . ' x ' . $_is[1] . '</strong></span>';
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_footer();

        // Check for any custom images left over - not part of the skin
        if (isset($_cust) && is_array($_cust) && count($_cust) > 0) {

            if (isset($k)) {
                $k++;
            }
            else {
                $k = 0;
            }
            jrCore_page_divider();

            $dat             = array();
            $dat[1]['title'] = 'custom image';
            $dat[1]['width'] = '30%';
            $dat[2]['title'] = 'options';
            $dat[2]['width'] = '40%';
            $dat[3]['title'] = 'upload new custom image';
            $dat[3]['width'] = '30%';
            jrCore_page_table_header($dat);

            $dir = jrCore_get_media_directory(0);
            $num = 0;
            foreach ($_cust as $img => $size) {
                $dat = array();
                $_is = getimagesize("{$dir}/{$t_tag}{$skin}_{$img}");
                $w   = $_is[0];
                $h   = $_is[1];
                $l   = false;
                if (isset($h) && $h > 100) {
                    $w = (($w / $h) * 100);
                    $h = 100;
                    $l = true;
                }
                elseif (isset($w) && $w > 100) {
                    $h = (($h / $w) * 100);
                    $w = 100;
                    $l = true;
                }
                $url             = "{$_conf['jrCore_base_url']}/data/media/0/0/{$t_tag}{$skin}_{$img}";
                $dat[1]['title'] = '<div style="width:120px;display:inline-block;vertical-align:middle;">';
                if ($l) {
                    $dat[1]['title'] .= "<a href=\"{$url}\" data-lightbox=\"images\" title=\"{$img}\"><img src=\"{$url}?r=" . mt_rand() . "\" height=\"{$h}\" width=\"{$w}\" alt=\"{$img}\" title=\"{$img}\" style=\"margin-bottom:6px\"></a>";
                }
                else {
                    $dat[1]['title'] .= "<img src=\"{$url}?r=" . mt_rand() . "\" height=\"{$h}\" width=\"{$w}\" alt=\"{$img}\" title=\"{$img}\" style=\"margin-bottom:6px\">";
                }

                $dat[1]['title'] .= '</div>';
                $dat[1]['class'] = 'center';
                $embed           = '<br><br><strong>Template Code (no wrap):</strong><br><div id="debug_log" style="width:390px;padding:0;word-wrap:break-word">{jrCore_image module=&quot;' . $skin . '&quot; image=&quot;' . $img . '&quot}</div>';
                $dat[2]['title'] = jrCore_page_button("d{$num}", 'delete image', "if (confirm('Are you sure you want to delete this custom image?')){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$curl}/skin_image_delete_save/{$u_tag}={$skin}/name={$img}')}") . $embed;
                $dat[2]['class'] = 'center';
                $dat[3]['title'] = '<input type="hidden" name="name_' . $k . '" value="' . $img . '"><input type="file" name="file_' . $k . '"><br><span class="sublabel" style="word-wrap:break-word"><strong>' . str_replace('_', '', $img) . '</strong> - <strong>' . $_is[0] . ' x ' . $_is[1] . '</strong></span>';
                jrCore_page_table_row($dat);
                $num++;
                $k++;
            }
            jrCore_page_table_footer();
        }
    }

    // Upload new image
    $imax = array_keys(jrImage_get_allowed_image_sizes());
    $imax = end($imax);
    $_tmp = array(
        'name'       => "new_images",
        'type'       => 'file',
        'label'      => 'additional images',
        'help'       => 'Upload custom images for use in your templates',
        'text'       => 'Select Images to Upload',
        'extensions' => 'png,gif,jpg,jpeg',
        'multiple'   => true,
        'required'   => false,
        'max'        => $imax
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_set_no_header_or_footer();
    return jrCore_page_display(true);
}

/**
 * Display Available templates for editing
 * @param $skin string Skin directory
 * @param $_post array request parameters
 * @param $_user array active user info
 * @param $_conf array global config
 * @return mixed
 */
function jrCore_show_skin_templates($skin, $_post, $_user, $_conf)
{
    unset($_SESSION['template_cancel_url']);
    // Generate our output
    jrCore_page_skin_tabs($skin, 'templates');

    $murl     = jrCore_get_module_url('jrCore');
    $subtitle = '<select name="skin_jumper" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $_conf['jrCore_base_url'] . "/{$murl}/skin_admin/templates/skin='+ $(this).val())\">";
    $_tmpm    = jrCore_get_skins();
    foreach ($_tmpm as $skin_dir => $_skin) {
        $_mta = jrCore_skin_meta_data($skin_dir);
        $name = (isset($_mta['title'])) ? $_mta['title'] : $skin_dir;
        if ($skin_dir == $_post['skin']) {
            $subtitle .= '<option value="' . $skin_dir . '" selected="selected"> ' . $name . "</option>\n";
        }
        else {
            $subtitle .= '<option value="' . $skin_dir . '"> ' . $name . "</option>\n";
        }
    }
    $subtitle .= '</select>';
    jrCore_page_banner('Templates', $subtitle);
    jrCore_get_form_notice();

    // See if we have a search string
    $_tpls = glob(APP_DIR . "/skins/{$skin}/*.tpl");
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        // Search through templates
        foreach ($_tpls as $k => $full_file) {
            $temp = file_get_contents($full_file);
            if (!stristr(' ' . $temp, $_post['search_string'])) {
                unset($_tpls[$k]);
            }
        }
    }

    jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/templates/skin={$skin}");

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'action'       => "skin_admin_save/templates/skin={$skin}"
    );
    jrCore_form_create($_tmp);

    $diff = jrCore_get_diff_binary();

    // Start our output
    $dat             = array();
    $dat[1]['title'] = 'name';
    $dat[1]['width'] = '60%';
    $dat[2]['title'] = 'active';
    $dat[2]['width'] = '5%';
    $dat[3]['title'] = 'updated';
    $dat[3]['width'] = '25%';
    $dat[4]['title'] = 'modify';
    $dat[4]['width'] = '5%';
    if ($diff) {
        $dat[5]['title'] = 'compare';
        $dat[5]['width'] = '3%';
        $dat[6]['title'] = 'reset';
        $dat[6]['width'] = '2%';
    }
    else {
        $dat[6]['title'] = 'reset';
        $dat[6]['width'] = '5%';
    }
    jrCore_page_table_header($dat);

    // Get all of our actual template files...
    if (isset($_tpls) && is_array($_tpls)) {

        // Get templates from database to see if we have customized any of them
        $tbl = jrCore_db_table_name('jrCore', 'template');
        $req = "SELECT template_id, template_module, template_updated, template_user, template_active, template_name FROM {$tbl} WHERE template_module = '" . jrCore_db_escape($skin) . "'";
        $_tp = jrCore_db_query($req, 'template_name');
        $url = jrCore_get_module_url('jrCore');

        // Go through templates on file system
        foreach ($_tpls as $full_file) {
            $dat             = array();
            $tpl_name        = basename($full_file);
            $dat[1]['title'] = $tpl_name;

            $dat[1]['class'] = (isset($_post) && isset($_post['hl']) && $_post['hl'] == $tpl_name) ? 'field-hilight' : '';

            if (isset($_tp[$tpl_name])) {
                $checked = '';
                if (isset($_tp[$tpl_name]['template_active']) && $_tp[$tpl_name]['template_active'] == '1') {
                    $checked = ' checked="checked"';
                }
                $chk_name        = str_replace('.tpl', '', $tpl_name);
                $dat[2]['title'] = '<input type="hidden" name="' . $chk_name . '_template_active" value="off"><input type="checkbox" name="' . $chk_name . '_template_active" class="form-checkbox"' . $checked . '>';
                $dat[3]['title'] = jrCore_format_time($_tp[$tpl_name]['template_updated']) . '<br>' . $_tp[$tpl_name]['template_user'];
                $dat[3]['class'] = 'center nowrap';
            }
            else {
                $dat[2]['title'] = '&nbsp;';
                $dat[3]['title'] = '&nbsp;';
            }
            $dat[2]['class'] = 'center';

            if (isset($_tp[$tpl_name])) {
                $dat[4]['title'] = jrCore_page_button("m{$tpl_name}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/template_modify/skin={$skin}/id=" . $_tp[$tpl_name]['template_id'] . "')");
                if ($diff) {
                    $dat[5]['title'] = jrCore_page_button("c{$tpl_name}", 'compare', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/template_compare/skin={$skin}/id=" . $_tp[$tpl_name]['template_id'] . "')");
                }
                $dat[6]['title'] = jrCore_page_button("r{$tpl_name}", 'reset', "if (confirm('Are you sure you want to reset this template to the default?')){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/template_reset_save/skin={$skin}/id=" . $_tp[$tpl_name]['template_id'] . "')}");
            }
            else {
                $dat[4]['title'] = jrCore_page_button("m{$tpl_name}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/template_modify/skin={$skin}/template={$tpl_name}')");
                if ($diff) {
                    $dat[5]['title'] = jrCore_page_button("c{$tpl_name}", 'compare', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/template_compare/skin={$skin}/id=" . urlencode($tpl_name) . "')");
                }
                $dat[6]['title'] = '&nbsp;';
            }
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>There were no templates found to match your search criteria!</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    // Save Template Updates - this small hidden field needs to be here
    // otherwise the form will not work - this is due to the fact the checkbox
    // elements in the table were created outside of jrCore_form_field_create
    $_tmp = array(
        'name'     => "save_template_updates",
        'type'     => 'hidden',
        'required' => 'true',
        'validate' => 'onoff',
        'value'    => 'on'
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_set_no_header_or_footer();
    return jrCore_page_display(true);
}

/**
 * Show info about a skin
 * @param $skin string skin directory
 * @param $_post array request parameters
 * @param $_user array active user info
 * @param $_conf array global config
 * @return mixed
 */
function jrCore_show_skin_info($skin, $_post, $_user, $_conf)
{
    // Generate our output
    jrCore_page_skin_tabs($skin, 'info');
    $murl     = jrCore_get_module_url('jrCore');
    $subtitle = '<select name="skin_jumper" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $_conf['jrCore_base_url'] . "/{$murl}/skin_admin/info/skin='+ $(this).val())\">";
    $_tmpm    = jrCore_get_skins();
    foreach ($_tmpm as $skin_dir => $_skin) {
        $_mta = jrCore_skin_meta_data($skin_dir);
        $titl = (isset($_mta['title'])) ? $_mta['title'] : $skin_dir;
        if ($skin_dir == $_post['skin']) {
            $subtitle .= '<option value="' . $_post['skin'] . '" selected="selected"> ' . $titl . "</option>\n";
        }
        else {
            $subtitle .= '<option value="' . $skin_dir . '"> ' . $titl . "</option>\n";
        }
    }
    $subtitle .= '</select>';

    $_mta = jrCore_skin_meta_data($skin);
    $name = (isset($_mta['title'])) ? $_mta['title'] : $skin;
    jrCore_page_banner($name, $subtitle);

    $_opt = array('description', 'version', 'developer');
    $onum = count($_opt) + 1;
    if (is_file(APP_DIR . "/skins/{$skin}/readme.html")) {
        $onum++;
    }
    if (is_file(APP_DIR . "/skins/{$skin}/license.html")) {
        $onum++;
    }
    $temp = '<div id="info_box" class="p20"><table><tr><td rowspan="' . $onum . '" style="width:10%" class="item info_img"><img src="' . $_conf['jrCore_base_url'] . '/skins/' . $skin . '/icon.png" width="128" height="128" alt="' . $_mta['name'] . '"></td>';
    foreach ($_opt as $k => $key) {
        $text = (isset($_mta[$key])) ? $_mta[$key] : 'undefined';
        if ($k > 0) {
            $temp .= '<tr>';
        }
        $temp .= '<td style="width:15%" class="page_table_cell p3 right"><strong>' . $key . ':</strong></td><td style="width:85%" class="page_table_cell p3 left">' . $text . '</td>';
        if ($k > 0) {
            $temp .= '</tr>';
        }
    }
    // Skin Directory
    $temp .= '<tr><td class="page_table_cell p3 right"><strong>directory:</strong></td><td class="page_table_cell p3 left">' . $skin . '</td></tr>';
    if (is_file(APP_DIR . "/skins/{$skin}/license.html")) {
        $temp .= '<tr><td style="width:15%" class="page_table_cell p3 right"><strong>license:</strong></td><td style="width:85%" class="page_table_cell p3 left">';
        $temp .= "<a href=\"{$_conf['jrCore_base_url']}/{$murl}/license/skin={$skin}\" onclick=\"popwin('{$_conf['jrCore_base_url']}/{$_post['module_url']}/license/skin={$skin}','license',800,500,'yes');return false\"><span style=\"text-decoration:underline;\">Click to View License</span></a></td></tr>";
    }

    // See if this module has a readme associated with it
    if (is_file(APP_DIR . "/skins/{$skin}/readme.html")) {
        $text = "<a href=\"{$_conf['jrCore_base_url']}/skins/{$skin}/readme.html\" onclick=\"popwin('{$_conf['jrCore_base_url']}/skins/{$skin}/readme.html','readme',800,500,'yes');return false\"><span style=\"text-decoration:underline;\">Click to View Skin Notes</span></a>";
        $temp .= '<tr><td style="width:15%" class="page_table_cell p3 right"><strong>notes:</strong></td><td style="width:85%" class="page_table_cell p3 left">' . $text . '</td></tr>';
    }
    $temp .= '</table>';

    // Check for screen shots
    foreach (range(1, 4) as $n) {
        if (is_file(APP_DIR . "/skins/{$skin}/img/screenshot{$n}.jpg")) {
            if (!isset($_img)) {
                $_img = array();
            }
            $_img[] = "{$_conf['jrCore_base_url']}/skins/{$skin}/img/screenshot{$n}.jpg";
        }
    }
    if (isset($_img) && is_array($_img)) {
        $perc = round(100 / count($_img), 2);
        $temp .= '<br><table><tr>';
        foreach ($_img as $k => $shot) {
            $temp .= "<td style=\"width:{$perc}%;padding:6px;\"><a href=\"{$shot}\" data-lightbox=\"screenshots\" title=\"screenshot {$k}\"><img src=\"{$shot}\" class=\"img_scale\" alt=\"screenshot {$k}\"></a></td>";
        }
        $temp .= '</tr></table></div>';
    }

    jrCore_page_custom($temp);

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'action'       => "skin_admin_save/info/skin={$skin}"
    );
    jrCore_form_create($_tmp);

    // Active Skin
    $act = 'off';
    if (isset($_conf['jrCore_active_skin']) && $_conf['jrCore_active_skin'] == $skin) {
        $act = 'on';
    }
    $_tmp = array(
        'name'     => 'skin_active',
        'label'    => 'set as active skin',
        'help'     => "If you would like to use this skin for your site, check this option and save.",
        'type'     => 'checkbox',
        'value'    => $act,
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Show delete option if skin directory is writable by the web user
    // and the skin is NOT the active skin
    if ((!is_dir(APP_DIR . "/skins/{$skin}") || is_writable(APP_DIR . "/skins/{$skin}")) && $act != 'on') {
        $_tmp = array(
            'name'     => 'skin_delete',
            'label'    => 'delete skin',
            'help'     => "If you would like to remove this skin from your system, check this option and save.<br><br><strong>WARNING!</strong> This will <strong>permanently</strong> delete the skin files from your system!",
            'type'     => 'checkbox',
            'value'    => 'off',
            'validate' => 'onoff'
        );
        jrCore_form_field_create($_tmp);
    }

    jrCore_page_set_no_header_or_footer();
    return jrCore_page_display(true);
}

/**
 * Parse a Jamroom CSS File and return an array of CSS items
 * @param $file string File to parse
 * @param $section string Active section
 * @return array
 */
function jrCore_parse_css_file($file, $section)
{
    if (!is_file($file)) {
        return false;
    }
    $_tmp = file($file);
    if (!$_tmp || !is_array($_tmp)) {
        return false;
    }
    $_out = array();

    // Characters we strip from title and help lines
    $ignore_next_item = false;
    $_strip           = array('@title', '@help', '/*', '*/');
    foreach ($_tmp as $line) {

        $line = trim($line);
        // End comment on separate line
        if (strlen($line) < 1 || strpos($line, '*') === 0 || strpos($line, '@ignore')) {
            continue;
        }

        // Comment
        elseif (strpos($line, '/*') === 0) {
            if (!strpos($line, '@') && $section != 'extra') {
                continue;
            }
            // We have a comment with info..
            if (strpos($line, '@title')) {
                $title = trim(str_replace($_strip, '', $line));
            }
            elseif (strpos($line, '@help')) {
                $help = trim(str_replace($_strip, '', $line));
            }
            elseif (strpos($line, '@ignore')) {
                $ignore_next_item = true;
            }
            continue;
        }

        // Element/Class/ID - begin
        elseif (strpos($line, '{') && !strpos($line, '{$jamroom') && !strpos($line, '_img_url}/')) {
            if ((!isset($title) && $section != 'extra') || $ignore_next_item) {
                continue;
            }
            if (isset($title) && $section == 'extra') {
                continue;
            }
            $name = trim(substr($line, 0, strpos($line, '{')));
            if ($section == 'extra' && !isset($title)) {
                $title = $name;
                $help  = false;
            }
            if (!$ignore_next_item) {
                $_out[$name] = array(
                    'title' => isset($title) ? $title : '',
                    'help'  => isset($help) ? $help : '',
                    'rules' => array()
                );
            }
        }

        // Element/Class/ID - end
        elseif (strpos($line, '}') === 0) {
            if ($ignore_next_item) {
                $ignore_next_item = false;
                continue;
            }
            if (!isset($title)) {
                continue;
            }
            if (isset($name)) {
                unset($name);
            }
            if (isset($title)) {
                unset($title);
            }
            if (isset($help)) {
                unset($help);
            }
        }

        // Rules
        elseif (isset($name) && strpos($line, ':')) {
            if ($ignore_next_item) {
                continue;
            }
            if (!isset($title)) {
                continue;
            }
            list($rule, $value) = explode(':', $line, 2);
            $rule                        = trim($rule);
            $value                       = ltrim(rtrim(trim($value), ';'), '#');
            $_out[$name]['rules'][$rule] = $value;
        }
    }
    return $_out;
}

/**
 * Create a Global Config screen for a module/skins
 * @param $type string module|skin
 * @param $module string module|skin name
 * @param $_post array Post info
 * @param $_user array User array
 * @param $_conf array Global Config
 * @return mixed
 */
function jrCore_show_global_settings($type, $module, $_post, $_user, $_conf)
{
    global $_mods;

    // Get this module's config entries from settings
    $tbl = jrCore_db_table_name('jrCore', 'setting');
    $req = "SELECT * FROM {$tbl} WHERE `module` = '" . jrCore_db_escape($module) . "' AND `type` != 'hidden' ORDER BY `order` ASC, `section` ASC, `name` ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('notice', 'There are no visible global config options for this module');
    }

    // See if we have a custom config display function
    if (jrCore_module_is_active($module) && !function_exists("{$module}_config") && is_file(APP_DIR . "/modules/{$module}/config.php")) {
        require_once APP_DIR . "/modules/{$module}/config.php";
    }
    $func = "{$module}_config_display";
    if (function_exists($func)) {
        $func($_post, $_user, $_conf);
    }

    // Check for incoming highlighting
    if (isset($_post['hl']) && is_array($_post['hl'])) {
        foreach ($_post['hl'] as $fld) {
            jrCore_form_field_hilight($fld);
        }
    }
    elseif (isset($_post['hl']) && strlen($_post['hl']) > 0) {
        jrCore_form_field_hilight($_post['hl']);
    }

    // Generate our output
    $frs = false;
    if ($type == 'module') {
        jrCore_page_admin_tabs($module, 'global');
        $action = 'admin_save/global';
    }
    else {
        jrCore_page_skin_tabs($_post['skin'], 'global');
        $_tb = array();
        $act = false;
        $frs = (isset($_post['section'])) ? $_post['section'] : false;
        foreach ($_rt as $_set) {
            if (isset($_set['section']{0}) && !isset($_tb["{$_set['section']}"])) {
                $_tb["{$_set['section']}"] = array(
                    "label" => $_set['section'],
                    "url"   => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/global/skin={$_post['skin']}/section=" . urlencode($_set['section'])
                );
                if (isset($_post['section']) && $_post['section'] == $_set['section']) {
                    $_tb["{$_set['section']}"]['active'] = 1;
                    $act                                 = true;
                }
                if (!$frs) {
                    $frs = $_set['section'];
                }
            }
        }
        if (count($_tb) > 0) {
            // We've got sections
            if (!$act) {
                // Default to first section
                $_tb[$frs]['active'] = true;
            }
            jrCore_page_tab_bar($_tb);
        }
        $action = 'skin_admin_save/global';
    }

    // Setup our module jumper
    $url = jrCore_get_module_url('jrCore');
    if ($type == 'skin') {
        $subtitle = '<select name="designer_form" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $_conf['jrCore_base_url'] . "/{$url}/skin_admin/global/skin='+ $(this).val())\">";
        $_tmpm    = jrCore_get_skins();
        foreach ($_tmpm as $skin_dir => $_skin) {
            if (is_file(APP_DIR . "/skins/{$skin_dir}/config.php")) {
                $_mta = jrCore_skin_meta_data($skin_dir);
                $name = (isset($_mta['title'])) ? $_mta['title'] : $skin_dir;
                if ($skin_dir == $_post['skin']) {
                    $subtitle .= '<option value="' . $_post['skin'] . '" selected="selected"> ' . $name . "</option>\n";
                }
                else {
                    $subtitle .= '<option value="' . $skin_dir . '"> ' . $name . "</option>\n";
                }
            }
        }
    }
    else {
        $subtitle = '<select name="designer_form" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $_conf['jrCore_base_url'] . "/'+ $(this).val() +'/admin/global')\">";
        $_tmpm    = array();
        foreach ($_mods as $mod_dir => $_info) {
            $_tmpm[$mod_dir] = $_info['module_name'];
        }
        asort($_tmpm);
        foreach ($_tmpm as $mod_dir => $title) {
            if (!jrCore_module_is_active($mod_dir)) {
                continue;
            }
            if (is_file(APP_DIR . "/modules/{$mod_dir}/config.php")) {
                if ($mod_dir == $_post['module']) {
                    $subtitle .= '<option value="' . $_post['module_url'] . '" selected="selected"> ' . $title . "</option>\n";
                }
                else {
                    $murl = jrCore_get_module_url($mod_dir);
                    $subtitle .= '<option value="' . $murl . '"> ' . $title . "</option>\n";
                }
            }
        }
    }
    $subtitle .= '</select>';
    jrCore_page_banner('Global Settings', $subtitle);

    // See if we are disabled
    if ($type == 'module' && !jrCore_module_is_active($module)) {
        jrCore_set_form_notice('notice', 'This module is currently disabled');
    }
    elseif (jrCore_module_is_active('jrDeveloper') && $module == 'jrCore' && isset($_conf['jrDeveloper_developer_mode']) && $_conf['jrDeveloper_developer_mode'] == 'on') {
        $durl = jrCore_get_module_url('jrDeveloper');
        jrCore_set_form_notice('notice', "Developer Mode is <strong>enabled</strong> - caching is disabled!<br><a href=\"{$_conf['jrCore_base_url']}/{$durl}/admin/global\"><span style=\"text-decoration:underline;\">Click here to modify the Developer Tools global settings.</span></a>", false);
    }
    jrCore_get_form_notice();

    if ($_rt && is_array($_rt) && count($_rt) > 0) {
        // Form init
        $_tmp = array(
            'submit_value' => 'save changes',
            'action'       => $action
        );
        if ($type != 'module') {
            $_tmp['form_ajax_submit'] = false;
        }
        jrCore_form_create($_tmp);

        foreach ($_rt as $_field) {
            if ($frs) {
                if (isset($_field['section']) && $_field['section'] == $frs) {
                    jrCore_form_field_create($_field);
                }
            }
            else {
                jrCore_form_field_create($_field);
            }
        }
    }
    jrCore_page_set_no_header_or_footer();
    return jrCore_page_display(true);
}

/**
 * Show the TOOLS section for a module
 * @param $module string module directory name
 * @param $_post array request parameters
 * @param $_user array active user info
 * @param $_conf array global config
 * @return mixed
 */
function jrCore_show_module_tools($module, $_post, $_user, $_conf)
{
    global $_mods;

    // Get registered tool views
    $_tool = jrCore_get_registered_module_features('jrCore', 'tool_view');

    // Generate our output
    jrCore_page_admin_tabs($module, 'tools');

    // Setup our module jumper
    $subtitle = '<select name="module_jumper" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $_conf['jrCore_base_url'] . "/'+ $(this).val() +'/admin/tools')\">";
    $_tmpm    = array();
    foreach ($_mods as $mod_dir => $_info) {
        $_tmpm[$mod_dir] = $_info['module_name'];
    }
    asort($_tmpm);
    foreach ($_tmpm as $mod_dir => $title) {
        if (!jrCore_module_is_active($mod_dir)) {
            continue;
        }
        if (isset($_tool[$mod_dir]) || jrCore_db_get_prefix($mod_dir)) {
            if ($mod_dir == $_post['module']) {
                $subtitle .= '<option value="' . $_post['module_url'] . '" selected="selected"> ' . $title . "</option>\n";
            }
            else {
                $murl = jrCore_get_module_url($mod_dir);
                $subtitle .= '<option value="' . $murl . '"> ' . $title . "</option>\n";
            }
        }
    }
    $subtitle .= '</select>';

    jrCore_page_banner("Tools", $subtitle);
    if (!jrCore_module_is_active($module)) {
        jrCore_set_form_notice('notice', 'This module is currently disabled');
    }
    jrCore_get_form_notice();

    if ((!isset($_tool[$module]) || !is_array($_tool[$module])) && !jrCore_db_get_prefix($module)) {
        jrCore_notice_page('error', 'there are no registered tool views for this module!');
    }
    // Check for DataStore browser
    if (jrCore_db_get_prefix($module)) {
        // DataStore enabled - check to see if this module is already registering a browser
        $_tmp = jrCore_get_registered_module_features('jrCore', 'tool_view');
        if (!isset($_tmp[$module]) || !isset($_tmp[$module]['browser'])) {
            jrCore_page_tool_entry("{$_conf['jrCore_base_url']}/{$_post['module_url']}/browser", 'DataStore Browser', "Modify and Delete items in this module's DataStore");
        }
    }
    if (isset($_tool) && is_array($_tool) && isset($_tool[$module])) {
        foreach ($_tool[$module] as $view => $_inf) {
            $onc = (isset($_inf[2])) ? $_inf[2] : null;
            if (strpos($view, $_conf['jrCore_base_url']) === 0) {
                jrCore_page_tool_entry($view, $_inf[0], $_inf[1], $onc, '_blank');
            }
            else {
                jrCore_page_tool_entry("{$_conf['jrCore_base_url']}/{$_post['module_url']}/{$view}", $_inf[0], $_inf[1], $onc);
            }
        }
    }
    jrCore_page_set_no_header_or_footer();
    return jrCore_page_display(true);
}

/**
 * Show the templates for a module
 * @param $module string module directory name
 * @param $_post array request parameters
 * @param $_user array active user info
 * @param $_conf array global config
 * @return mixed
 */
function jrCore_show_module_templates($module, $_post, $_user, $_conf)
{
    global $_mods;
    unset($_SESSION['template_cancel_url']);
    // Generate our output
    jrCore_page_admin_tabs($module, 'templates');

    // Setup our module jumper
    $subtitle = '<select name="designer_form" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $_conf['jrCore_base_url'] . "/'+ $(this).val() +'/admin/templates')\">";
    $_tmpm    = array();
    foreach ($_mods as $mod_dir => $_info) {
        $_tmpm[$mod_dir] = $_info['module_name'];
    }
    asort($_tmpm);
    foreach ($_tmpm as $mod_dir => $title) {
        if (!jrCore_module_is_active($mod_dir)) {
            continue;
        }
        if (is_dir(APP_DIR . "/modules/{$mod_dir}/templates")) {
            if ($mod_dir == $_post['module']) {
                $subtitle .= '<option value="' . $_post['module_url'] . '" selected="selected"> ' . $title . "</option>\n";
            }
            else {
                $murl = jrCore_get_module_url($mod_dir);
                $subtitle .= '<option value="' . $murl . '"> ' . $title . "</option>\n";
            }
        }
    }
    $subtitle .= '</select>';
    jrCore_page_banner('Templates', $subtitle);
    if (!jrCore_module_is_active($module)) {
        jrCore_set_form_notice('notice', 'This module is currently disabled');
    }
    jrCore_get_form_notice();

    // Get templates
    $_tpls = glob(APP_DIR . "/modules/{$module}/templates/*.tpl");

    // Get templates from database to see if we have customized any of them
    $tbl = jrCore_db_table_name('jrCore', 'template');
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $req = "SELECT template_id, template_module, template_updated, template_user, template_active, template_name, template_body FROM {$tbl} WHERE template_module = '" . jrCore_db_escape($module) . "'";
    }
    else {
        $req = "SELECT template_id, template_module, template_updated, template_user, template_active, template_name FROM {$tbl} WHERE template_module = '" . jrCore_db_escape($module) . "'";
    }
    $_tp = jrCore_db_query($req, 'template_name');

    // See if we have a search string
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        // Search through templates
        foreach ($_tpls as $k => $full_file) {
            $fname = basename($full_file);
            $found = false;

            // Match in file name
            if (stripos(' ' . $fname, $_post['search_string'])) {
                $found = true;
            }

            // Match in custom contents
            if (isset($_tp[$fname]['template_body']{0})) {
                $temp = file_get_contents($_tp[$fname]['template_body']);
                if (stristr(' ' . $temp, $_post['search_string'])) {
                    $found = true;
                }
            }

            // Match in actual file contents
            $temp = file_get_contents($full_file);
            if (stristr(' ' . $temp, $_post['search_string'])) {
                $found = true;
            }
            if (!$found) {
                unset($_tpls[$k]);
            }
        }
    }
    jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/templates");

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'action'       => 'admin_save/templates'
    );
    jrCore_form_create($_tmp);

    $diff = jrCore_get_diff_binary();

    // Start our output
    $dat             = array();
    $dat[1]['title'] = 'name';
    $dat[1]['width'] = '55%';
    $dat[2]['title'] = 'active';
    $dat[2]['width'] = '5%';
    $dat[3]['title'] = 'updated';
    $dat[3]['width'] = '25%';
    $dat[4]['title'] = 'modify';
    $dat[4]['width'] = '5%';
    if ($diff) {
        $dat[5]['title'] = 'compare';
        $dat[5]['width'] = '3%';
        $dat[6]['title'] = 'reset';
        $dat[6]['width'] = '2%';
    }
    else {
        $dat[6]['title'] = 'reset';
        $dat[6]['width'] = '5%';
    }
    jrCore_page_table_header($dat);

    // Get all of our actual template files...
    if (isset($_tpls) && is_array($_tpls) && count($_tpls) > 0) {

        $url = jrCore_get_module_url('jrCore');

        // Go through templates on file system
        foreach ($_tpls as $full_file) {
            $dat             = array();
            $tpl_name        = basename($full_file);
            $dat[1]['title'] = $tpl_name;
            $dat[1]['class'] = (isset($_post) && $_post['hl'] == $tpl_name) ? 'field-hilight' : '';
            if (isset($_tp[$tpl_name])) {
                $checked = '';
                if (isset($_tp[$tpl_name]['template_active']) && $_tp[$tpl_name]['template_active'] == '1') {
                    $checked = ' checked="checked"';
                }
                $chk_name        = str_replace('.tpl', '', $tpl_name);
                $dat[2]['title'] = '<input type="hidden" name="' . $chk_name . '_template_active" value="off"><input type="checkbox" name="' . $chk_name . '_template_active" class="form-checkbox"' . $checked . '>';
                $dat[3]['title'] = jrCore_format_time($_tp[$tpl_name]['template_updated']) . '<br>' . $_tp[$tpl_name]['template_user'];
                $dat[3]['class'] = 'center nowrap';
            }
            else {
                $dat[2]['title'] = '&nbsp;';
                $dat[3]['title'] = '&nbsp;';
            }
            $dat[2]['class'] = 'center';
            if (isset($_tp[$tpl_name])) {
                $dat[4]['title'] = jrCore_page_button("m{$tpl_name}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/template_modify/id=" . $_tp[$tpl_name]['template_id'] . "')");
                if ($diff) {
                    $dat[5]['title'] = jrCore_page_button("c{$tpl_name}", 'compare', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/template_compare/id=" . $_tp[$tpl_name]['template_id'] . "')");
                }
                $dat[6]['title'] = jrCore_page_button("r{$tpl_name}", 'reset', "if (confirm('Are you sure you want to reset this template to the default?')){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/template_reset_save/id=" . $_tp[$tpl_name]['template_id'] . "')}");
            }
            else {
                $dat[4]['title'] = jrCore_page_button("m{$tpl_name}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/template_modify/template={$tpl_name}')");
                if ($diff) {
                    $dat[5]['title'] = jrCore_page_button("c{$tpl_name}", 'compare', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/template_compare/id=" . urlencode($tpl_name) . "')");
                }
                $dat[6]['title'] = '&nbsp;';
            }
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>There were no templates found to match your search criteria!</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    // Save Template Updates - this small hidden field needs to be here
    // otherwise the form will not work - this is due to the fact the checkbox
    // elements in the table were created outside of jrCore_form_field_create
    $_tmp = array(
        'name'     => "save_template_updates",
        'type'     => 'hidden',
        'required' => 'true',
        'validate' => 'onoff',
        'value'    => 'on'
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_set_no_header_or_footer();
    return jrCore_page_display(true);
}

/**
 * Show the info page for a module
 * @param $module string module directory name
 * @param $_post array request parameters
 * @param $_user array active user info
 * @param $_conf array global config
 * @return mixed
 */
function jrCore_show_module_info($module, $_post, $_user, $_conf)
{
    global $_mods;

    // Generate our output
    jrCore_page_admin_tabs($module, 'info');

    // Setup our module jumper
    $subtitle = '<select name="module_jumper" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $_conf['jrCore_base_url'] . "/'+ $(this).val() +'/admin/info')\">";
    $_tmpm    = array();
    foreach ($_mods as $mod_dir => $_info) {
        $_tmpm[$mod_dir] = $_info['module_name'];
    }
    asort($_tmpm);
    foreach ($_tmpm as $mod_dir => $title) {
        if ($mod_dir == $_post['module']) {
            $subtitle .= '<option value="' . $_post['module_url'] . '" selected="selected"> ' . $title . "</option>\n";
        }
        else {
            $murl = jrCore_get_module_url($mod_dir);
            $subtitle .= '<option value="' . $murl . '"> ' . $title . "</option>\n";
        }
    }
    $subtitle .= '</select>';
    if (!jrCore_module_is_active($module)) {
        // We have to bring in our include...
        require_once APP_DIR . "/modules/{$module}/include.php";
    }
    $_mta = jrCore_module_meta_data($module);
    jrCore_page_banner($_mta['name'], $subtitle);

    // See if we exist
    if (!is_dir(APP_DIR . "/modules/{$module}")) {
        jrCore_set_form_notice('error', 'Unable to find module files - re-install or delete from system');
    }
    // See if we are locked
    elseif (isset($_mta['locked']) && $_mta['locked'] == '1') {
        jrCore_set_form_notice('notice', 'This module is an integral part of the Core system and cannot be disabled or removed');
    }
    // See if we are disabled
    elseif (!jrCore_module_is_active($module)) {
        jrCore_set_form_notice('notice', 'This module is currently disabled');
    }

    jrCore_get_form_notice();

    // Show information about this module
    $pass = jrCore_get_option_image('pass');
    $fail = jrCore_get_option_image('fail');
    $_opt = array('description', 'version', 'requires', 'developer', 'license');
    $onum = count($_opt) + 1;
    if (is_file(APP_DIR . "/modules/{$module}/readme.html")) {
        $onum++;
    }
    $temp = '<div id="info_box" class="p20"><table><tr><td rowspan="' . $onum . '" style="width:10%" class="item info_img"><img src="' . $_conf['jrCore_base_url'] . '/modules/' . $module . '/icon.png" width="128" height="128" alt="' . $_mta['name'] . '"></td>';
    $sact = true;
    foreach ($_opt as $k => $key) {

        switch ($key) {

            case 'requires':
                $text = '';
                if (isset($_mta['requires']{0})) {
                    $_mrq = array();
                    $_req = explode(',', $_mta[$key]);
                    foreach ($_req as $rmod) {
                        $rmod = trim($rmod);
                        $rver = false;
                        if (strpos($rmod, ':')) {
                            list($rmod, $rver) = explode(':', $rmod, 2);
                            $rmod = trim($rmod);
                            $rver = trim($rver);
                        }
                        // Module is installed and active
                        if (jrCore_module_is_active($rmod) && !$rver) {
                            $_mrq[] .= $pass . '&nbsp;' . $_mods[$rmod]['module_name'];
                        }
                        // Module is installed and active - version is good
                        elseif (jrCore_module_is_active($rmod) && $rver && version_compare($_mods[$rmod]['module_version'], $rver) !== -1) {
                            $_mrq[] .= $pass . '&nbsp;' . $_mods[$rmod]['module_name'] . ' ' . $rver;
                        }
                        // Module is installed and active - version is too low
                        elseif (jrCore_module_is_active($rmod) && $rver && version_compare($_mods[$rmod]['module_version'], $rver) === -1) {
                            $_mrq[] .= $fail . '<a href="' . $_conf['jrCore_base_url'] . '/' . $_mods['jrMarket']['module_url'] . '/system_update" style="text-decoration:underline;">&nbsp;' . $_mods[$rmod]['module_name'] . '&nbsp;must at least version ' . $rver . '!</a>';
                        }
                        elseif (isset($_mods[$rmod])) {
                            $_mrq[] .= $fail . '<a href="' . $_conf['jrCore_base_url'] . '/' . $_mods[$rmod]['module_url'] . '/admin/info" style="text-decoration:underline;">&nbsp;' . $_mods[$rmod]['module_name'] . '&nbsp;not active!</a>';
                        }
                        else {
                            $_mrq[] .= $fail . '<a href="' . $_conf['jrCore_base_url'] . '/' . $_mods['jrMarket']['module_url'] . '/browse/module/search_string=' . $rmod . '" style="text-decoration:underline;">&nbsp;' . $rmod . '&nbsp;not found!</a>';
                            $sact = false;
                        }
                    }
                    $text = implode('&nbsp;&nbsp;', $_mrq);
                }
                break;

            case 'license':
                $murl = jrCore_get_module_url($module);
                $text = "<a href=\"{$_conf['jrCore_base_url']}/{$murl}/license\" onclick=\"popwin('{$_conf['jrCore_base_url']}/{$murl}/license','license',800,500,'yes');return false\"><span style=\"text-decoration:underline;\">Click to View License</span></a>";
                break;

            default:
                $text = (isset($_mta[$key])) ? $_mta[$key] : 'undefined';
                break;
        }

        if (strlen($text) > 0) {
            if ($k > 0) {
                $temp .= '<tr>';
            }
            $temp .= '<td style="width:15%" class="page_table_cell p3 right"><strong>' . $key . ':</strong></td><td style="width:85%" class="page_table_cell p3 left">' . $text . '</td>' . "\n";
            if ($k > 0) {
                $temp .= '</tr>';
            }
        }
    }
    // Module Directory
    $temp .= '<tr><td class="page_table_cell p3 right"><strong>directory:</strong></td><td class="page_table_cell p3 left">' . $module . '</td></tr>';

    // See if this module has a readme associated with it
    if (is_file(APP_DIR . "/modules/{$module}/readme.html")) {
        $text = "<a href=\"{$_conf['jrCore_base_url']}/modules/{$module}/readme.html\" onclick=\"popwin('{$_conf['jrCore_base_url']}/modules/{$module}/readme.html','readme',800,500,'yes');return false\"><span style=\"text-decoration:underline;\">Click to View Module Notes</span></a>";
        $temp .= '<tr><td class="page_table_cell p3 right"><strong>notes:</strong></td><td class="page_table_cell p3 left">' . $text . '</td></tr>';
    }
    $temp .= '</table></div>';
    jrCore_page_custom($temp);

    jrCore_page_section_header('module settings');

    // Module settings
    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'action'       => 'admin_save/info'
    );
    jrCore_form_create($_tmp);

    // Module URL
    if (!isset($_mta['url_change']) || $_mta['url_change'] !== false) {
        $_tmp = array(
            'name'     => 'new_module_url',
            'label'    => 'module URL',
            'help'     => "The Module URL setting determines how the module will be accessed - i.e. {$_conf['jrCore_base_url']}/<strong>{$_mods[$module]['module_url']}</strong>/",
            'type'     => 'text',
            'value'    => $_mods[$module]['module_url'],
            'validate' => 'url_name'
        );
        jrCore_form_field_create($_tmp);
    }
    else {
        jrCore_page_custom($_mta['url'], 'module URL');
    }

    // Module Category
    $_tmp = array(
        'name'     => 'new_module_category',
        'label'    => 'module category',
        'help'     => "If you would like to change the category for this module, enter a new category name here.<br><br><strong>NOTE:</strong> Category name must consist of letters, numbers and spaces only.",
        'type'     => 'text',
        'value'    => $_mods[$module]['module_category'],
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    // Module Active
    if (!isset($_mta['locked']) || $_mta['locked'] != '1') {
        if ($sact) {
            $act = 'on';
            $tag = 'disable';
            if (!jrCore_module_is_active($module)) {
                $act = 'off';
                $tag = 'enable';
            }
            $_tmp = array(
                'name'     => 'module_active',
                'label'    => 'module active',
                'help'     => "You can <strong>{$tag}</strong> this module by checking this option and saving.",
                'type'     => 'checkbox',
                'value'    => $act,
                'validate' => 'onoff'
            );
            jrCore_form_field_create($_tmp);

            if (!jrCore_module_is_active($module)) {
                $_tmp = array(
                    'name'     => 'module_delete',
                    'label'    => 'delete module',
                    'help'     => "If you would like to remove this module from your system, check this option and save.<br><br><strong>WARNING!</strong> This will <strong>permanently</strong> delete the module files from your system!",
                    'type'     => 'checkbox',
                    'value'    => 'off',
                    'validate' => 'onoff'
                );
                jrCore_form_field_create($_tmp);
            }
        }
        else {
            jrCore_set_form_notice('error', 'This module has required dependencies that are not met, and cannot be enabled');
            jrCore_get_form_notice();

            $act = 'on';
            $tag = 'disable';
            if (!jrCore_module_is_active($module)) {
                $act = 'off';
                $tag = 'enable';
            }
            $_tmp = array(
                'name'     => 'module_active',
                'label'    => 'module active',
                'help'     => "You can <strong>{$tag}</strong> this module by checking this option and saving.",
                'type'     => 'checkbox',
                'value'    => $act,
                'validate' => 'onoff'
            );
            jrCore_form_field_create($_tmp);

            if (!jrCore_module_is_active($module)) {
                $_tmp = array(
                    'name'     => 'module_delete',
                    'label'    => 'delete module',
                    'help'     => "If you would like to remove this module from your system, check this option and save.<br><br><strong>WARNING!</strong> This will <strong>permanently</strong> delete the module files from your system!",
                    'type'     => 'checkbox',
                    'value'    => 'off',
                    'validate' => 'onoff'
                );
                jrCore_form_field_create($_tmp);
            }
        }
    }

    // See if we are showing developer information
    if (isset($_conf['jrDeveloper_developer_mode']) && $_conf['jrDeveloper_developer_mode'] == 'on') {

        // EVENTS

        // First - get any event triggers we are providing
        $_tmp = jrCore_get_flag('jrcore_event_triggers');
        $_out = array();
        if (isset($_tmp) && is_array($_tmp)) {
            foreach ($_tmp as $k => $v) {
                if (strpos($k, "{$module}_") === 0) {
                    $name        = str_replace("{$module}_", '', $k);
                    $_out[$name] = array('desc' => $v);
                }
            }
        }

        // Next, find out how many listeners we have
        if (isset($_out) && is_array($_out) && count($_out) > 0) {
            $_tmp = jrCore_get_flag('jrcore_event_listeners');
            if (isset($_tmp) && is_array($_tmp)) {
                foreach ($_tmp as $k => $v) {
                    if (strpos($k, "{$module}_") === 0) {
                        $name                     = str_replace("{$module}_", '', $k);
                        $_out[$name]['listeners'] = implode('<br>', $v);
                    }
                }
            }
        }

        if (isset($_out) && is_array($_out) && count($_out) > 0) {
            ksort($_out);
            jrCore_page_section_header('module events');
            $dat             = array();
            $dat[1]['title'] = 'trigger name';
            $dat[1]['width'] = '16%';
            $dat[2]['title'] = 'description';
            $dat[2]['width'] = '56%';
            $dat[3]['title'] = 'listeners';
            $dat[3]['width'] = '28%';
            jrCore_page_table_header($dat);

            foreach ($_out as $event => $_params) {
                $dat             = array();
                $dat[1]['title'] = $event;
                $dat[2]['title'] = (isset($_params['desc'])) ? $_params['desc'] : '-';
                $dat[2]['class'] = 'center';
                $dat[3]['title'] = (isset($_params['listeners'])) ? $_params['listeners'] : '-';
                $dat[3]['class'] = 'center';
                jrCore_page_table_row($dat);
            }
            jrCore_page_table_footer();
        }
    }
    jrCore_page_set_no_header_or_footer();
    return jrCore_page_display(true);
}

/**
 * Generate "Bigview" dashboard view
 * @param $_post array posted info
 * @param $_user array viewing user info
 * @param $_conf array global config
 */
function jrCore_dashboard_bigview($_post, $_user, $_conf)
{
    global $_mods;
    // See what our layout is
    $_cfg = false;
    $rows = 2;
    $cols = 4;
    if (isset($_conf['jrCore_dashboard_config']{1})) {
        $_cfg = json_decode($_conf['jrCore_dashboard_config'], true);
        if (isset($_cfg['rows']) && jrCore_checktype($_cfg['rows'], 'number_nz')) {
            $rows = (int) $_cfg['rows'];
        }
        if (isset($_cfg['cols']) && jrCore_checktype($_cfg['cols'], 'number_nz')) {
            $cols = (int) $_cfg['cols'];
        }
    }

    // Our default panel setup
    $_def = array(
        0 => array(
            0 => array(
                't' => 'total profiles',
                'f' => 'jrProfile_dashboard_panels'
            ),
            1 => array(
                't' => 'signups today',
                'f' => 'jrProfile_dashboard_panels'
            ),
            2 => array(
                't' => 'users online',
                'f' => 'jrUser_dashboard_panels'
            ),
            3 => array(
                't' => 'queue depth',
                'f' => 'jrCore_dashboard_panels'
            )
        ),
        1 => array(
            0 => array(
                't' => 'memory used',
                'f' => 'jrCore_dashboard_panels'
            ),
            1 => array(
                't' => 'disk usage',
                'f' => 'jrCore_dashboard_panels'
            ),
            2 => array(
                't' => 'CPU count',
                'f' => 'jrCore_dashboard_panels'
            ),
            3 => array(
                't' => '5 minute load',
                'f' => 'jrCore_dashboard_panels'
            )
        )
    );
    foreach ($_def as $row => $_cols) {
        foreach ($_cols as $col => $_inf) {
            if (!isset($_cfg['_panels'][$row][$col])) {
                $_cfg['_panels'][$row][$col] = $_inf;
            }
        }
    }
    ksort($_cfg['_panels'], SORT_NUMERIC);

    // Get registered Graph functions
    $_tmp = jrCore_get_registered_module_features('jrGraph', 'graph_config');
    $_url = array();
    if ($_tmp && is_array($_tmp)) {
        foreach ($_tmp as $mod => $_fnc) {
            foreach ($_fnc as $name => $_inf) {
                $_url[$name] = jrCore_get_module_url($mod);
            }
        }
    }

    $_html = array();
    $_func = array();
    $width = round((100 / $cols), 2);
    for ($r = 0; $r < $rows; $r++) {
        $dat = array();
        for ($c = 0; $c < $cols; $c++) {
            $dat[$c]['title'] = '';
            if (isset($_cfg['_panels'][$r][$c])) {
                $ttl = $_cfg['_panels'][$r][$c]['t'];
                if (strpos($_cfg['_panels'][$r][$c]['t'], 'item count')) {
                    $mod = trim(jrCore_string_field($_cfg['_panels'][$r][$c]['t'], 1));
                    if (isset($_mods[$mod])) {
                        $ttl = $_mods[$mod]['module_name'] . ' count';
                    }
                }
                $dat[$c]['title'] = '<div class="bignum_stat_cell">' . $ttl;
                $fnc              = $_cfg['_panels'][$r][$c]['f'];
                if (function_exists($fnc)) {
                    $_func[$r][$c] = $fnc($_cfg['_panels'][$r][$c]['t']);
                    $out           = $_func[$r][$c];
                    if (isset($out['graph']) && !jrCore_is_mobile_device()) {
                        $id = "g{$r}{$c}";
                        if (strpos($out['graph'], '/')) {
                            list($mu,) = explode('/', $out['graph'], 2);
                            $mu = $_url[$mu];
                        }
                        else {
                            $mu = $_url["{$out['graph']}"];
                        }
                        if (strlen($mu) > 0) {
                            $_html[] = "<div id=\"{$id}\" style=\"width:750px;height:400px;display:none;bottom:0;\"></div>";
                            $dat[$c]['title'] .= "<div class=\"bignum_stat\"><a href=\"{$_conf['jrCore_base_url']}/{$mu}/graph/{$out['graph']}\" onclick=\"jrCore_dashboard_disable_reload(60);jrGraph_modal_graph('#{$id}', '{$mu}', '{$out['graph']}', 'modal'); return false\">" . jrCore_get_icon_html('stats', 16) . '</a></div>';
                        }
                    }
                }
                $dat[$c]['title'] .= '</div>';
            }
            $dat[$c]['width'] = "{$width}%";
        }
        jrCore_page_table_header($dat, 'bigtable');

        $dat = array();
        for ($c = 0; $c < $cols; $c++) {
            if (isset($_cfg['_panels'][$r][$c])) {
                $out = false;
                if (isset($_func[$r][$c])) {
                    $out = $_func[$r][$c];
                    if ($out && is_array($out)) {
                        $dat[$c]['title'] = $out['title'];
                        if (isset($out['class'])) {
                            $dat[$c]['class'] = "bignum bignum" . ($c + 1) . " {$out['class']}";
                        }
                        else {
                            $dat[$c]['class'] = "bignum bignum" . ($c + 1);
                        }
                    }
                }
                if (!$out) {
                    $dat[$c]['title'] = '!';
                    $dat[$c]['class'] = "bignum bignum" . ($c + 1) . ' error';
                }
            }
            else {
                $dat[$c]['title'] = '?';
                $dat[$c]['class'] = "bignum bignum" . ($c + 1);
            }
            $dat[$c]['class'] .= "\" id=\"id-{$r}-{$c}";
        }
        jrCore_page_table_row($dat);
        jrCore_page_table_footer();
    }

    $html = jrCore_parse_template('dashboard_panels.tpl', array(), 'jrCore');
    jrCore_page_custom($html);

    if (is_array($_html)) {
        jrCore_page_custom(implode("\n", $_html));
    }

    if (jrUser_is_master()) {
        $_tmp = array("$('.bignum').click(function(e) { e.stopPropagation(); jrCore_dashboard_disable_reload(60); jrCore_dashboard_panel($(this).attr('id')); return false });");
        jrCore_create_page_element('javascript_ready_function', $_tmp);
    }
}

/**
 * Show Pending Items Dashboard view
 * @param $_post array Global $_post
 * @param $_user array Viewing user array
 * @param $_conf array Global config
 */
function jrCore_dashboard_pending($_post, $_user, $_conf)
{
    // Get our pending items
    $tbl = jrCore_db_table_name('jrCore', 'pending');
    $req = "SELECT * FROM {$tbl} WHERE pending_module != 'jrAction'";
    $_ex = false;
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $_post['search_string'] = trim(urldecode($_post['search_string']));
        $str                    = jrCore_db_escape($_post['search_string']);
        $req .= " AND pending_data LIKE '%{$str}%' ";
        $_ex = array('search_string' => $_post['search_string']);
    }
    $req .= 'ORDER BY pending_id ASC';

    // find how many lines we are showing
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }
    $_rt = jrCore_db_paged_query($req, $_post['p'], 12, 'NUMERIC');

    // start our html output
    jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/pending");

    $dat             = array();
    $dat[1]['title'] = '<input type="checkbox" class="form_checkbox" onclick="$(\'.pending_checkbox\').prop(\'checked\',$(this).prop(\'checked\'));">';
    $dat[1]['width'] = '1%;';
    $dat[2]['title'] = 'date';
    $dat[2]['width'] = '10%;';
    $dat[3]['title'] = 'item';
    $dat[3]['width'] = '36%;';
    $dat[4]['title'] = 'profile';
    $dat[4]['width'] = '12%;';
    $dat[5]['title'] = 'user';
    $dat[5]['width'] = '12%;';
    $dat[6]['title'] = 'approve';
    $dat[6]['width'] = '3%;';
    $dat[7]['title'] = 'reject';
    $dat[7]['width'] = '3%;';
    $dat[8]['title'] = 'delete';
    $dat[8]['width'] = '3%;';
    jrCore_page_table_header($dat);
    unset($dat);

    $url = jrCore_get_module_url('jrCore');
    if (isset($_rt['_items']) && is_array($_rt['_items'])) {

        foreach ($_rt['_items'] as $_pend) {
            $_data           = json_decode($_pend['pending_data'], true);
            $murl            = jrCore_get_module_url($_pend['pending_module']);
            $dat             = array();
            $dat[1]['title'] = '<input type="checkbox" class="form_checkbox pending_checkbox" name="' . $_pend['pending_id'] . '">';
            $dat[2]['title'] = jrCore_format_time($_pend['pending_created']);
            $dat[2]['class'] = 'nowrap';
            $dat[3]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$_data['user']['profile_url']}/{$murl}/{$_pend['pending_item_id']}\" target=\"_blank\">{$_data['user']['profile_url']}/{$murl}/{$_pend['pending_item_id']}</a>";
            $dat[4]['title'] = $_data['user']['profile_name'];
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = $_data['user']['user_name'];
            $dat[5]['class'] = 'center';
            $dat[6]['title'] = jrCore_page_button("a{$_pend['pending_id']}", 'approve', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/pending_item_approve/{$_pend['pending_module']}/id={$_pend['pending_item_id']}')");
            $dat[7]['title'] = jrCore_page_button("r{$_pend['pending_id']}", 'reject', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/pending_item_reject/{$_pend['pending_module']}/id={$_pend['pending_item_id']}')");
            $dat[8]['title'] = jrCore_page_button("d{$_pend['pending_id']}", 'delete', "if(confirm('Are you sure you want to delete this item? No notice will be sent.')){jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/pending_item_delete/{$_pend['pending_module']}/id={$_pend['pending_item_id']}')}");
            jrCore_page_table_row($dat);
        }

        $sjs = "var v = $('input:checkbox.pending_checkbox:checked').map(function(){ return this.name; }).get().join(',')";
        $tmp = jrCore_page_button("all", 'approve checked', "{$sjs};jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/pending_item_approve/all/id='+ v)");
        $tmp .= '&nbsp;' . jrCore_page_button("delete", 'delete checked', "if (confirm('Are you sure you want to delete all checked items?')){ {$sjs};jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/pending_item_delete/all/id='+ v )}");

        $dat             = array();
        $dat[1]['title'] = $tmp;
        jrCore_page_table_row($dat);

        jrCore_page_table_pager($_rt, $_ex);
    }
    else {
        $dat = array();
        if (!empty($_post['search_string'])) {
            $dat[1]['title'] = '<p>There were no Pending Items found to match your search criteria</p>';
        }
        else {
            $dat[1]['title'] = '<p>There are no pending items to show</p>';
        }
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
}

/**
 * Display DS Browser
 * @param $mode string dashboard|admin where browser is being run from
 * @param $_post array Global $_post
 * @param $_user array Viewing user array
 * @param $_conf array Global config
 * @return bool
 */
function jrCore_dashboard_browser($mode, $_post, $_user, $_conf)
{
    global $_mods;

    // Get modules that have registered a custom datastore browser
    $add = '';

    $url = jrCore_get_current_url();
    $_tmp = jrCore_get_registered_module_features('jrCore', 'data_browser');
    if (isset($_tmp["{$_post['module']}"])) {
        if (!isset($_post['vk'])) {
            $add .= jrCore_page_button('raw', 'view keys', "jrCore_window_location('{$url}/vk=true')");
        }
        else {
            $url  = jrCore_strip_url_params($url, array('vk'));
            $add .= jrCore_page_button('raw', 'view browser', "jrCore_window_location('{$url}')");
        }
    }

    // Create a Quick Jump list for custom forms for this module
    $j_url = 'browser';
    if (strpos(jrCore_get_local_referrer(), 'dashboard')) {
        $j_url = 'dashboard/browser';
    }
    $add  .= '<select name="data_browser" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $_conf['jrCore_base_url'] . "/'+ $(this).val() +'/{$j_url}')\">\n";
    $_tmpm = array();
    foreach ($_mods as $mod_dir => $_inf) {
        if (!jrCore_module_is_active($mod_dir)) {
            continue;
        }
        if (isset($_inf['module_prefix']) && strlen($_inf['module_prefix']) > 0) {
            $_tmpm[$mod_dir] = $_inf['module_name'];
        }
    }
    asort($_tmpm);
    foreach ($_tmpm as $module => $title) {
        $murl = jrCore_get_module_url($module);
        if ($module == $_post['module']) {
            $add .= '<option value="' . $murl . '" selected="selected"> ' . $title . "</option>\n";
        }
        else {
            $add .= '<option value="' . $murl . '"> ' . $title . "</option>\n";
        }
    }
    $add .= '</select>';

    $val = '';
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $val = $_post['search_string'];
    }

    jrCore_page_banner('data browser', $add);
    jrCore_get_form_notice();
    if (isset($mode) && $mode == 'dashboard') {
        jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard/browser", $val);
    }
    else {
        jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browser", $val);
    }

    // See if this module has registered it's own Browser
    if (isset($_tmp["{$_post['module']}"]) && !isset($_post['vk'])) {
        $func = array_keys($_tmp["{$_post['module']}"]);
        $func = (string) reset($func);
        if (function_exists($func)) {
            $func($_post, $_user, $_conf);
        }
        else {
            jrCore_page_notice('error', "invalid custom browser function defined for {$_post['module']}");
        }
    }
    else {

        // get our items
        $_pr = array(
            'search'                       => array(
                '_created > 0'
            ),
            'pagebreak'                    => (isset($_COOKIE['jrcore_pager_rows']) && jrCore_checktype($_COOKIE['jrcore_pager_rows'], 'number_nz')) ? (int) $_COOKIE['jrcore_pager_rows'] : 6,
            'page'                         => 1,
            'order_by'                     => array(
                '_item_id' => 'desc'
            ),
            'exclude_jrUser_keys'          => true,
            'exclude_jrProfile_quota_keys' => true,
            'ignore_pending'               => true,
            'privacy_check'                => false
        );
        if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
            $_pr['page'] = (int) $_post['p'];
        }
        // See we have a search condition
        $_ex = false;
        if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
            $_ex = array('search_string' => $_post['search_string']);
            // Check for passing in a specific key name for search
            if (strpos($_post['search_string'], ':')) {
                list($sf, $ss) = explode(':', $_post['search_string'], 2);
                $_post['search_string'] = $ss;
                if (is_numeric($ss)) {
                    $_pr['search'][] = "{$sf} = {$ss}";
                }
                else {
                    $_pr['search'][] = "{$sf} like {$ss}%";
                }
            }
            else {
                $_pr['search'][] = "% like {$_post['search_string']}";
            }
        }
        $_us = jrCore_db_search_items($_post['module'], $_pr);

        // See if we have detail pages for this module
        $view = false;
        if (is_file(APP_DIR . "/modules/{$_post['module']}/templates/item_detail.tpl")) {
            $view = true;
        }

        // Start our output
        $dat             = array();
        $dat[1]['title'] = 'id';
        $dat[1]['width'] = '5%';
        $dat[2]['title'] = 'info';
        $dat[2]['width'] = '78%';
        $dat[3]['title'] = 'modify';
        $dat[3]['width'] = '2%';
        jrCore_page_table_header($dat);

        if (isset($_us['_items']) && is_array($_us['_items'])) {
            foreach ($_us['_items'] as $_itm) {
                $dat = array();
                switch ($_post['module']) {
                    case 'jrUser':
                        $iid = $_itm['_user_id'];
                        break;
                    case 'jrProfile':
                        $iid = $_itm['_profile_id'];
                        break;
                    default:
                        $iid = $_itm['_item_id'];
                        break;
                }
                $pfx             = jrCore_db_get_prefix($_post['module']);
                $dat[1]['title'] = $iid;
                $dat[1]['class'] = 'center';
                $_tm             = array();
                ksort($_itm);
                $master_user = false;
                $admin_user  = false;
                $_rep        = array("\n", "\r", "\n\r");
                foreach ($_itm as $k => $v) {
                    if (strpos($k, $pfx) !== 0) {
                        continue;
                    }
                    switch ($k) {
                        case '_user_id':
                        case '_profile_id':
                        case '_item_id':
                        case 'user_password':
                        case 'user_old_password':
                        case 'user_validate':
                            break;
                        case 'user_group':
                            switch ($v) {
                                case 'master':
                                    $master_user = true;
                                    break;
                                case 'admin':
                                    $admin_user = true;
                                    break;
                            }
                        // NOTE: We fall through on purpose here!
                        default:
                            if (isset($v) && is_array($v)) {
                                $v = json_encode($v);
                            }
                            if (is_numeric($v) && strlen($v) === 10) {
                                $v = jrCore_format_time($v);
                            }
                            else {
                                $v = strip_tags(str_replace($_rep, ' ', $v));
                            }
                            if (strlen($v) > 80) {
                                $v = substr($v, 0, 80) . '...';
                            }
                            if (isset($_post['search_string'])) {
                                // See if we are searching a specific field
                                if (isset($sf)) {
                                    if ($k == $sf) {
                                        $v = jrCore_hilight_string($v, str_replace('%', '', $_post['search_string']));
                                    }
                                }
                                else {
                                    $v = jrCore_hilight_string($v, str_replace('%', '', $_post['search_string']));
                                }
                            }
                            $_tm[] = "<span class=\"ds_browser_key\">{$k}:</span> <span class=\"ds_browser_value\">{$v}</span>";
                            break;
                    }
                }
                $dat[3]['title'] = implode('<br>', $_tm);
                $_att            = array(
                    'style' => 'width:70px;'
                );

                $dat[4]['title'] = '';
                if ($view && isset($_itm["{$pfx}_title_url"])) {
                    $url             = "{$_conf['jrCore_base_url']}/{$_itm['profile_url']}/{$_post['module_url']}/{$iid}/{$_itm["{$pfx}_title_url"]}";
                    $dat[4]['title'] = jrCore_page_button("v{$iid}", 'view', "jrCore_window_location('{$url}')", $_att) . '<br><br>';
                }

                $url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browser_item_update/id={$iid}";
                $dat[4]['title'] .= jrCore_page_button("m{$iid}", 'modify', "jrCore_window_location('{$url}')", $_att) . '<br><br>';

                // Check and see if we are browsing User Accounts - if so, admin users cannot delete
                // admin or master accounts.  Master cannot delete other master accounts.
                $add = false;
                if (jrUser_is_master() && !$master_user) {
                    $add = true;
                }
                elseif (jrUser_is_admin() && !$master_user && !$admin_user) {
                    $add = true;
                }
                if ($add) {
                    $dat[4]['title'] .= jrCore_page_button("d{$iid}", 'delete', "if (confirm('Are you sure you want to delete this item? The item will be permanently DELETED!')){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/browser_item_delete/id={$iid}')}", $_att);
                }
                $dat[4]['class'] = 'center';
                jrCore_page_table_row($dat);
            }
            jrCore_page_table_pager($_us, $_ex);
        }
        else {
            $dat = array();
            if (isset($_post['search_string'])) {
                $dat[1]['title'] = '<p>No Results found for your Search Criteria.</p>';
            }
            else {
                $dat[1]['title'] = '<p>No Items found in DataStore!</p>';
            }
            $dat[1]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_footer();
    }
}

/**
 * Tabs for use o the Activity Log, Debug Log and Error Log views
 * @param $active string Active Tab
 */
function jrCore_master_log_tabs($active)
{
    global $_conf, $_post;
    $_tabs                    = array();
    $_tabs['activity']        = array(
        'label' => 'activity log',
        'url'   => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/activity_log"
    );
    $_tabs['debug']           = array(
        'label' => 'debug log',
        'url'   => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/debug_log"
    );
    $_tabs['error']           = array(
        'label' => 'PHP error log',
        'url'   => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/php_error_log"
    );
    $_tabs[$active]['active'] = true;
    jrCore_page_tab_bar($_tabs);
}

/**
 * Run a performance check
 */
function jrCore_run_performance_check()
{
    ini_set('max_execution_time', 3600);

    // Clean up
    jrCore_db_truncate_datastore('jrCore');

    // Start
    $beg = explode(' ', microtime());
    $beg = $beg[1] + $beg[0];
    $stt = $beg;

    $_tm = array();

    //------------------
    // CPU
    //------------------
    $a = 0;
    for ($i = 0; $i < 10000000; $i++) {
        $a += $i;
    }
    $end        = explode(' ', microtime());
    $end        = $end[1] + $end[0];
    $_tm['cpu'] = round($end - $stt, 2);
    $beg = $end;

    //------------------
    // DATABASE
    //------------------

    $tbi = jrCore_db_table_name('jrCore', 'item');
    $tbl = jrCore_db_table_name('jrCore', 'item_key');
    $con = jrCore_db_connect();

    // Create 1000 Objects
    foreach (range(1, 2000) as $num) {
        $req = "INSERT INTO {$tbi} (`_item_id`) VALUES (0)";
        mysqli_query($con, $req) or jrCore_notice('CRI', 'Query Error: ' . mysqli_error($con));
        $iid = (int) mysqli_insert_id($con);
        if ($iid > 0) {
            $mod = ($num % 2);
            $_dt = array(
                'core_num'    => $num,
                'core_title'  => "Object {$num} Title",
                'core_title2' => "Object {$num} Title2",
                'core_string' => "Object {$num} String",
                'core_number' => intval("{$num}0"),
                'core_float'  => floatval("{$num}.{$num}"),
                'core_set'    => $mod
            );
            if ($mod == 1) {
                $_dt['core_one'] = 1;
            }
            if ($num == 2) {
                $_dt['core_exists'] = 1;
            }
            if ($num == 3) {
                $_dt['core_exists'] = 2;
            }
            $req = "INSERT INTO {$tbl} (`_item_id`,`key`,`index`,`value`) VALUES ";
            foreach ($_dt as $k => $v) {
                $req .= "('{$iid}','" . jrCore_db_escape($k) . "','0','" . jrCore_db_escape($v) . "'),";
            }
            $req = substr($req, 0, strlen($req) - 1);
            mysqli_query($con, $req) or jrCore_notice('CRI', 'Query Error: ' . mysqli_error($con));
        }
    }

    // Update 1000 Objects
    foreach (range(1, 2000) as $num) {
        $_dt = array(
            'core_num2'   => $num,
            'core_title3' => "Object {$num} Title",
            'core_title4' => "String: {$num}: " . jrCore_create_unique_string(500)
        );
        $req = "INSERT INTO {$tbl} (`_item_id`,`key`,`index`,`value`) VALUES ";
        foreach ($_dt as $k => $v) {
            $req .= "('{$num}','" . jrCore_db_escape($k) . "',0,'" . jrCore_db_escape($v) . "'),";
        }
        $req = substr($req, 0, strlen($req) - 1) . " ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
        mysqli_query($con, $req) or jrCore_notice('CRI', 'Query Error: ' . mysqli_error($con));
    }

    // Search Objects
    $i = 0;
    while ($i < 2000) {
        $req = "SELECT DISTINCT(a.`_item_id`) AS _item_id FROM {$tbl} a
            LEFT JOIN {$tbl} b ON (b.`_item_id` = a.`_item_id` AND b.`key` = 'core_num')
            LEFT JOIN {$tbl} c ON (c.`_item_id` = a.`_item_id` AND c.`key` = 'core_set')
            LEFT JOIN {$tbl} d ON (d.`_item_id` = a.`_item_id` AND d.`key` = 'core_string')
            LEFT JOIN {$tbl} e ON (e.`_item_id` = a.`_item_id` AND e.`key` = 'core_title')
                WHERE a.`key` = '_updated'
                  AND b.`value` > {$i}
                  AND c.`value` > {$i}
                  AND d.`value` LIKE '%tri%'
                  AND e.`value` LIKE '%itl%'
                ORDER BY a.`value` DESC LIMIT 10";
        mysqli_query($con, $req) or jrCore_notice('CRI', 'Query Error: ' . mysqli_error($con));
        $i++;
    }

    // Delete Objects
    foreach (range(1, 2000) as $num) {
        $req = "DELETE FROM {$tbi} WHERE `_item_id` = '{$num}'";
        mysqli_query($con, $req) or jrCore_notice('CRI', 'Query Error: ' . mysqli_error($con));
        $req = "DELETE FROM {$tbl} WHERE `_item_id` = '{$num}'";
        mysqli_query($con, $req) or jrCore_notice('CRI', 'Query Error: ' . mysqli_error($con));
    }

    $end       = explode(' ', microtime());
    $end       = $end[1] + $end[0];
    $_tm['db'] = round($end - $beg, 2);
    $beg = $end;

    // Reset
    jrCore_db_truncate_datastore('jrCore');

    //------------------
    // FILESYSTEM
    //------------------
    clearstatcache();
    $cdr = jrCore_get_module_cache_dir('jrCore');
    foreach (range(1, 1000) as $num) {
        $str = jrCore_create_unique_string(1024);
        jrCore_write_to_file("{$cdr}/performance_test.txt", "{$num}: {$str}\n", 'append');
    }
    // Read
    $num = 0;
    while ($num < 1000) {
        file_get_contents("{$cdr}/performance_test.txt");
        $num++;
    }
    unlink("{$cdr}/performance_test.txt");

    $end          = explode(' ', microtime());
    $end          = $end[1] + $end[0];
    $_tm['fs']    = round($end - $beg, 2);
    $_tm['total'] = round($end - $stt, 2);

    $tbl = jrCore_db_table_name('jrCore', 'performance');
    $req = "INSERT INTO {$tbl} (p_time, p_val) VALUES (UNIX_TIMESTAMP(), '" . jrCore_db_escape(json_encode($_tm)) . "')";
    jrCore_db_query($req);

    return $_tm;
}
