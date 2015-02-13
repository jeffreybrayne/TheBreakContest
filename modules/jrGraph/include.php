<?php
/**
 * Jamroom 5 Graph Support module
 *
 * copyright 2003 - 2014
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
 * @copyright 2012 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrGraph_meta
 */
function jrGraph_meta()
{
    $_tmp = array(
        'name'        => 'Graph Support',
        'url'         => 'graph',
        'version'     => '1.0.1',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'support for creating and displaying line, point and bar graphs',
        'category'    => 'media',
        'license'     => 'mpl',
        'priority'    => 10,
        'activate'    => true
    );
    return $_tmp;
}

/**
 * jrGraph_init
 */
function jrGraph_init()
{
    // Our graph module provides the "graph" magic view
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrGraph', 'graph', 'jrGraph_create_graph');

    // Register our JS/CSS
    jrCore_register_module_feature('jrCore', 'javascript', 'jrGraph', APP_DIR . '/modules/jrGraph/contrib/strftime/strftime.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrGraph', APP_DIR . '/modules/jrGraph/contrib/flot/jquery.flot.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrGraph', APP_DIR . '/modules/jrGraph/contrib/flot/jquery.flot.time.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrGraph', 'jrGraph.js');
    jrCore_register_module_feature('jrCore', 'css', 'jrGraph', 'jrGraph.css');

    return true;
}

//---------------------------------------------------------
// GRAPH FUNCTIONS
//---------------------------------------------------------

/**
 * Display a Graph for a given graph name (MAGIC VIEW)
 * @param $_post array Params from jrCore_parse_url();
 * @param $_user array User information
 * @param $_conf array Global config
 * @return bool Returns true
 */
function jrGraph_create_graph($_post, $_user, $_conf)
{
    // When a graph is requested, it will come in like:
    // <module>/graph/<graph_name>/<width>/<height>/ ... <params>
    // <params> is optional and depends on the <graph_name> to support
    // <graph_name> is registered by the <module> so we know what
    // function is being used to gather the graph data.
    $nam = false;
    $ttl = false;
    if (!isset($_post['name']) && isset($_post['_1'])) {
        $_post['name'] = $_post['_1'];
    }
    if (strpos($_post['name'], '|ds_items_by_day')) {
        list($mod, $nam) = explode('|', $_post['name']);
        $ttl = 'Items Created by Day';
    }
    else {
        $_tm = jrCore_get_registered_module_features('jrGraph', 'graph_config');
        if ($_tm && is_array($_tm)) {
            foreach ($_tm as $m => $_fnc) {
                foreach ($_fnc as $name => $_inf) {
                    if ($name == $_post['name']) {
                        $nam = $_post['name'];
                        $ttl = $_inf['title'];
                        // Valid group
                        if (isset($_inf['group'])) {
                            switch ($_inf['group']) {
                                case 'master':
                                    if (!jrUser_is_master()) {
                                        jrCore_page_not_found();
                                    }
                                    break;
                                case 'admin':
                                    if (!jrUser_is_admin()) {
                                        jrCore_page_not_found();
                                    }
                                    break;
                                case 'user':
                                    if (!jrUser_is_logged_in()) {
                                        jrCore_page_not_found();
                                    }
                                    break;
                            }
                        }
                        break;
                    }
                }
            }
        }
        $mod = $_post['module'];
    }
    jrCore_page_banner($ttl, '<div id="xyval"></div>');

    $_args = array(
        'module' => $mod,
        'name'   => $nam,
        'height' => '300px',
        'width'  => '100%'
    );
    if (jrCore_is_ajax_request()) {
        $_args['modal'] = 'modal';
    }
    $temp  = new StdClass();
    $html  = smarty_function_jrGraph_embed($_args, $temp);
    jrCore_page_custom($html);

    if (jrCore_is_ajax_request()) {
        jrCore_page_close_button('$.modal.close();');
        jrCore_page_set_no_header_or_footer();
    }
    else {
        jrCore_page_set_meta_header_only();
    }
    return jrCore_page_display(true);
}

/**
 * Generate a Graph
 * @param $module string Module graph is being created for
 * @param $name string Name of Graph
 * @param $params array additional parameters
 * @return string
 */
function jrGraph_generate($module, $name, $params = null)
{
    $_tm = jrCore_get_registered_module_features('jrGraph', 'graph_config');
    if (!$_tm || !isset($_tm[$module]) || !isset($_tm[$module][$name])) {
        jrCore_notice('error', 'invalid graph - graph name not registered by module');
    }
    $fnc = $_tm[$module][$name]['function'];
    if (!function_exists($fnc)) {
        jrCore_notice('error', 'invalid graph - graph function registered by module does not exist');
    }
    if (is_null($params)) {
        $params = array();
    }
    if (isset($_tm[$module][$name]['params']) && is_array($_tm[$module][$name]['params'])) {
        foreach ($_tm[$module][$name]['params'] as $k => $v) {
            $params[$k] = $v;
        }
    }
    if (!isset($params['days'])) {
        $params['days'] = 60;
    }
    $_dt = $fnc($module, $name, $params);

    // our plugin function will return a multidimensional array of data points
    // with time -> value series data - i.e.
    // array(
    //     '_sets' => array(
    //         array( <epoch_time> => <value> )
    //         array( <epoch_time> => <value> )
    //         ...
    //     ),
    //     'params' => array()
    // )

    $_rp = array(
        '_sets'     => array(),
        'xaxis'     => array(),
        'yaxis'     => array(),
        'clickable' => 0,
        'hoverable' => 0,
        'unique_id' => 't' . jrCore_create_unique_string(6),
        'height'    => (isset($params['height'])) ? $params['height'] : '350px',
        'width'     => (isset($params['width'])) ? $params['width'] : '100%'
    );
    if (isset($_dt['_sets']) && is_array($_dt['_sets'])) {

        if (isset($_dt['_options']['function'])) {
            // We have a custom click function...
            $_rp['function'] = $_dt['_options']['function'];
            unset($_dt['_options']['function']);
        }
        // SETS
        // Our timestamps must be in Epoch with milliseconds
        foreach ($_dt['_sets'] as $k => $_set) {

            $_rp['_sets'][$k] = array();
            if (isset($_set['_data']) && is_array($_set['_data'])) {
                $_rp['_sets'][$k]['data'] = array();
                $_set['_data'] = jrGraph_fill_data_gaps($_set['_data'], $params['days']);
                foreach ($_set['_data'] as $epc => $val) {
                    if (strlen($epc) != 13) {
                        $epc = ($epc * 1000);
                    }
                    $_rp['_sets'][$k]['data'][] = "[{$epc},{$val}]";
                }
            }
            // See if we have set options
            foreach ($_set as $sk => $sv) {
                switch ($sk) {

                    case 'minTickSize':
                        $_rp['xaxis']['minTickSize'] = $sv;
                        break;

                    case 'xticks':
                        $_rp['xaxis']['ticks'] = ($sv - 1);
                        unset($_set[$sk]);
                        break;

                    case 'yticks':
                        $_rp['yaxis']['ticks'] = $sv;
                        unset($_set[$sk]);
                        break;

                    case 'date_format':
                        // If we get the special "date_format" key we know our X-Axis is a Date
                        $_rp['xaxis']['mode']          = '"time"';
                        $_rp['xaxis']['tickFormatter'] = 'function(v,a) { return strftime("' . $sv . '", new Date(v)); }';
                        $_rp['tooltip_format']         = $sv;
                        unset($_set[$sk]);
                        break;

                    case 'type':

                        $lw = '';
                        if (isset($_set['lineWidth']) && jrCore_checktype($_set['lineWidth'], 'number_nz')) {
                            $lw = ", lineWidth: {$_set['lineWidth']}px";
                        }

                        $fc = '';
                        if (isset($_set['fillColor']) && strlen($_set['fillColor']) > 0) {
                            $fc = ', fill: true, fillColor: "' . trim(str_replace(array("'", '"'), '', $_set['fillColor'])) . '"';
                        }

                        $pr = '';
                        $sp = false;
                        if (isset($_set['pointRadius']) && jrCore_checktype($_set['pointRadius'], 'number_nz')) {
                            $pr = ", radius: {$_set['pointRadius']}";
                            $sp = true;
                        }

                        // We support: line, filled-line, bar and point
                        switch ($sv) {
                            case 'line':
                                $_rp['_sets'][$k]['lines'] = '{ show: true' . $lw . $fc . ' }';
                                if ($sp) {
                                    $_rp['_sets'][$k]['points'] = '{ show: true' . $pr . ' }';
                                    $_dt['_options']['grid']    = '{ margin: 20, hoverable: true }';
                                    $_rp['hoverable']           = 1;
                                }
                                break;

                            case 'bar':
                                $_rp['_sets'][$k]['bars'] = '{ show: true' . $fc . ' }';
                                break;

                            case 'point':
                                $_rp['_sets'][$k]['points'] = '{ show: true' . $pr . ' }';
                                $_dt['_options']['grid']    = '{ margin: 20, hoverable: true }';
                                $_rp['hoverable']           = 1;
                                break;
                        }
                        break;

                    case 'xaxis':
                    case 'yaxis':
                    case 'clickable':
                    case 'hoverable':
                    case 'font':
                        $_rp['_sets'][$k][$sk] = $sv;
                        break;

                    case 'color':
                    case 'label':
                    case 'shadowSize':
                    case 'highlightColor':
                        $_rp['_sets'][$k][$sk] = "'" . trim(str_replace(array("'", '"'), '', $sv)) . "'";
                        break;
                }
            }
        }
        unset($_dt['_sets']);

        // var d = [ [-373597200000, 315.71], [-370918800000, 317.45] ]
        foreach ($_rp['_sets'] as $k => $_set) {
            $_st = array();
            foreach ($_set as $sk => $sv) {
                switch ($sk) {
                    case 'data':
                        $sv = '[' . implode(',', $sv) . "]\n";
                        break;
                }
                $_st[] = "{$sk}: {$sv}";
            }
            $_rp['_sets'][$k] = '{' . implode(',', $_st) . '},';
        }

        // X-axis / Y-Axis
        foreach (array('xaxis', 'yaxis') as $axis) {
            // show: null or true/false
            // position: "bottom" or "top" or "left" or "right"
            // mode: null or "time" ("time" requires jquery.flot.time.js plugin)
            // timezone: null, "browser" or timezone (only makes sense for mode: "time")
            // color: null or color spec
            // tickColor: null or color spec
            // font: null or font spec object
            // min: null or number
            // max: null or number
            // autoscaleMargin: null or number
            // transform: null or fn: number -> number
            // inverseTransform: null or fn: number -> number
            // ticks: null or number or ticks array or (fn: axis -> ticks array)
            // tickSize: number or array
            // minTickSize: number or array
            // tickFormatter: (fn: number, object -> string) or string
            // tickDecimals: null or number
            // labelWidth: null or number
            // labelHeight: null or number
            // reserveSpace: null or true
            // tickLength: null or number
            // alignTicksWithAxis: null or number
            if (isset($_rp[$axis]) && count($_rp[$axis]) > 0) {
                $ax = '';
                foreach ($_rp[$axis] as $k => $v) {
                    $ax .= "{$k}: {$v},";
                }
                $_dt['_options'][$axis] = '{' . substr($ax, 0, strlen($ax) - 1) . '}';
                unset($ax);
            }
            unset($_rp[$axis]);
        }

        if (!isset($_dt['legend'])) {
            $_dt['legend'] = array(
                'container' => "#l{$_rp['unique_id']}"
            );
        }

        // Legend
        if (isset($_dt['legend'])) {
            if (!isset($_dt['legend']['container'])) {
                $_dt['legend']['container'] = '#graph-legend';
            }
            // show: boolean
            // labelFormatter: null or (fn: string, series object -> string)
            // labelBoxBorderColor: color
            // noColumns: number
            // position: "ne" or "nw" or "se" or "sw"
            // margin: number of pixels or [x margin, y margin]
            // backgroundColor: null or color
            // backgroundOpacity: number between 0 and 1
            // container: null or jQuery object/DOM element/jQuery expression
            // sorted: null/false, true, "ascending", "descending", "reverse", or a comparator
            $lg = '';
            foreach ($_dt['legend'] as $k => $v) {
                switch ($k) {
                    case 'position':
                    case 'container':
                        $lg .= "{$k}: '{$v}',";
                        break;
                    default:
                        $lg .= "{$k}: {$v},";
                        break;
                }
            }
            $_dt['_options']['legend'] = '{' . substr($lg, 0, strlen($lg) - 1) . '}';
        }

        // Plot options
        if (isset($_dt['_options']) && is_array($_dt['_options'])) {
            $op = '';
            foreach ($_dt['_options'] as $k => $v) {
                $op .= "{$k}: {$v},";
            }
            $_rp['options'] = ', {' . substr($op, 0, strlen($op) - 1) . '}';
            unset($_dt['_options']);
        }
    }
    $tpl = 'graph.tpl';
    $mod = 'jrGraph';
    if (is_file(APP_DIR . "/modules/{$module}/templates/graph_{$name}.tpl")) {
        $tpl = "graph_{$name}.tpl";
        $mod = $module;
    }
    return jrCore_parse_template($tpl, $_rp, $mod);
}

/**
 * Fill date gaps in the data with zeros
 * @param $_data array incoming data set
 * @param $number number of units to back fill
 * @param string $unit "days" or "hours"
 * @return mixed
 */
function jrGraph_fill_data_gaps($_data, $number, $unit = 'days')
{
    switch ($unit) {
        case 'days':
            $amt = 86400;
            $fmt = '%Y%m%d';
            break;
        case 'hours':
            $amt = 3600;
            $fmt = '%Y%m%d%H';
            break;
        default:
            return false;
            break;
    }
    $end = time();
    $beg = ($end - ($number * $amt));
    $_mp = array();
    while ($beg <= $end) {
        // first step - create map
        $time = strftime($fmt, $beg);
        $_mp[$time] = 0;
        $beg += $amt;
    }
    // Now - go through incoming data, and anywhere
    // we do not have a key in our data, add a zero in
    $_vl = array();
    foreach ($_data as $k => $v) {
        $time       = strftime($fmt, $k);
        $_vl[$time] = $v;
    }
    $_nw = array();
    foreach ($_mp as $k => $v) {
        if (isset($_vl[$k])) {
            $_mp[$k] = $_vl[$k];
        }
        $yr = substr($k, 0, 4);
        $mn = substr($k, 4, 2);
        $dy = substr($k, 6, 2);
        $tm = (string) mktime(0, 0, 0, $mn, $dy, $yr);
        $_nw["{$tm}"] = $_mp[$k];
    }
    unset($_mp, $_vl);
    return $_nw;
}

//---------------------------------------------------------
// SMARTY FUNCTIONS
//---------------------------------------------------------

/**
 * Embed a Graph in a template
 * @param $params array function parameters
 * @param $smarty object Smarty Object
 * @return string
 */
function smarty_function_jrGraph_embed($params, $smarty)
{
    if (!isset($params['module']{0})) {
        return 'jrGraph_embed: module parameter required';
    }
    if (!isset($params['name']) || strlen($params['name']) === 0) {
        return 'jrGraph_embed: graph name parameter required';
    }
    $out = jrGraph_generate($params['module'], $params['name'], $params);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}
