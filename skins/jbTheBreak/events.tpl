{assign var="selected" value="events"}
{assign var="spt" value="events"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="19" default="Events" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrSetActive('#default');
     });
</script>

{if isset($_post.option) && $_post.option == 'by_upcoming'}
    {assign var="order_by" value="event_date NUMERICAL_ASC"}
    {assign var="search" value="event_date >= `$smarty.now`"}
{elseif isset($_post.option) && $_post.option == 'by_ratings'}
    {assign var="order_by" value="event_rating_1_average_count NUMERICAL_DESC"}
{else}
    {assign var="order_by" value="_created desc"}
{/if}
{if isset($_post.month) || isset($_post.year)}
    {assign var="month" value=$_post.month}
    {assign var="year" value=$_post.year}
{else}
    {assign var="month" value=$smarty.now|jrCore_date_format:"%-m"}
    {assign var="year" value=$smarty.now|jrCore_date_format:"%Y"}
{/if}

<div class="container">
    <div class="row">

        <div class="col9">

            <div class="body_1 mr5">
                <div class="container">
                    <div class="row">
                        <div class="col12 last">
                            <div class="block_config">
                                <input type="button" value="Today" class="form_button" onclick="jrCore_window_location('{$jamroom_url}/events')">
                                <select class="form_select" style="width: auto" name="calendar_month" id="calendar_month" onchange="var m=this.options[this.selectedIndex].value; jrCore_window_location('{$jamroom_url}/events/month='+ m +'/year={$year}')">

                                    <option value="1" {if $month == "1"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="41" default="January"}</option>
                                    <option value="2" {if $month == "2"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="42" default="February"}</option>
                                    <option value="3" {if $month == "3"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="43" default="March"}</option>
                                    <option value="4" {if $month == "4"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="44" default="April"}</option>
                                    <option value="5" {if $month == "5"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="45" default="May"}</option>
                                    <option value="6" {if $month == "6"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="46" default="June"}</option>
                                    <option value="7" {if $month == "7"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="47" default="July"}</option>
                                    <option value="8" {if $month == "8"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="48" default="August"}</option>
                                    <option value="9" {if $month == "9"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="49" default="September"}</option>
                                    <option value="10" {if $month == "10"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="50" default="October"}</option>
                                    <option value="11" {if $month == "11"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="51" default="November"}</option>
                                    <option value="12" {if $month == "12"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="52" default="December"}</option>
                                </select>

                                {math equation="x - y" x=$year y="3" assign="tslyear"}
                                {math equation="x - y" x=$year y="2" assign="sslyear"}
                                {math equation="x - y" x=$year y="1" assign="slyear"}
                                {math equation="x + y" x=$year y="3" assign="tsnyear"}
                                {math equation="x + y" x=$year y="2" assign="ssnyear"}
                                {math equation="x + y" x=$year y="1" assign="snyear"}
                                <select class="form_select" style="width: auto" name="calendar_year" id="calendar_year" onchange="var y=this.options[this.selectedIndex].value; jrCore_window_location('{$jamroom_url}/events/month={$month}/year='+ y)">>
                                    <option value="{$tslyear}">{$tslyear}</option>
                                    <option value="{$sslyear}">{$sslyear}</option>
                                    <option value="{$slyear}">{$slyear}</option>
                                    <option value="{$year}" selected="selected">{$year}</option>
                                    <option value="{$snyear}">{$snyear}</option>
                                    <option value="{$ssnyear}">{$ssnyear}</option>
                                    <option value="{$tsnyear}">{$tsnyear}</option>
                                </select>
                            </div>
                            <div class="clear"></div>

                            <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="19" default="events"}</h2>

                            <div class="body_1 mb10">
                                <div class="container">
                                    <div class="row">
                                        <div class="col4">

                                            <div class="block">
                                                {if $month == '1'}
                                                    {assign var="lmonth" value="12"}
                                                    {math equation="x - y" x=$year y="1" assign="lyear"}
                                                {else}
                                                    {math equation="x - y" x=$month y="1" assign="lmonth"}
                                                {/if}

                                                {if $lmonth == 1}{jrCore_lang module="jrEvent" id="41" default="January" assign="lmonth_long"}{/if}
                                                {if $lmonth == 2}{jrCore_lang module="jrEvent" id="42" default="February" assign="lmonth_long"}{/if}
                                                {if $lmonth == 3}{jrCore_lang module="jrEvent" id="43" default="March" assign="lmonth_long"}{/if}
                                                {if $lmonth == 4}{jrCore_lang module="jrEvent" id="44" default="April" assign="lmonth_long"}{/if}
                                                {if $lmonth == 5}{jrCore_lang module="jrEvent" id="45" default="May" assign="lmonth_long"}{/if}
                                                {if $lmonth == 6}{jrCore_lang module="jrEvent" id="46" default="June" assign="lmonth_long"}{/if}
                                                {if $lmonth == 7}{jrCore_lang module="jrEvent" id="47" default="July" assign="lmonth_long"}{/if}
                                                {if $lmonth == 8}{jrCore_lang module="jrEvent" id="48" default="August" assign="lmonth_long"}{/if}
                                                {if $lmonth == 9}{jrCore_lang module="jrEvent" id="49" default="September" assign="lmonth_long"}{/if}
                                                {if $lmonth == 10}{jrCore_lang module="jrEvent" id="50" default="October" assign="lmonth_long"}{/if}
                                                {if $lmonth == 11}{jrCore_lang module="jrEvent" id="51" default="November" assign="lmonth_long"}{/if}
                                                {if $lmonth == 12}{jrCore_lang module="jrEvent" id="52" default="December" assign="lmonth_long"}{/if}

                                                <div class="center">
                                                    <h4>{$lmonth_long} - {if isset($lyear)}{$lyear}{else}{$year}{/if}</h4>
                                                </div>
                                                <div class="block_content" style="box-shadow: none;">
                                                    {if isset($lyear)}
                                                        {jrEvent_calendar month=$lmonth year=$lyear template="small_calendar.tpl" tpl_dir="jrEvent"}
                                                    {else}
                                                        {jrEvent_calendar month=$lmonth year=$year template="small_calendar.tpl" tpl_dir="jrEvent"}
                                                    {/if}
                                                </div>

                                            </div>

                                        </div>

                                        <div class="col4">

                                            <div class="block">

                                                {if $month == 1}{jrCore_lang module="jrEvent" id="41" default="January" assign="month_long"}{/if}
                                                {if $month == 2}{jrCore_lang module="jrEvent" id="42" default="February" assign="month_long"}{/if}
                                                {if $month == 3}{jrCore_lang module="jrEvent" id="43" default="March" assign="month_long"}{/if}
                                                {if $month == 4}{jrCore_lang module="jrEvent" id="44" default="April" assign="month_long"}{/if}
                                                {if $month == 5}{jrCore_lang module="jrEvent" id="45" default="May" assign="month_long"}{/if}
                                                {if $month == 6}{jrCore_lang module="jrEvent" id="46" default="June" assign="month_long"}{/if}
                                                {if $month == 7}{jrCore_lang module="jrEvent" id="47" default="July" assign="month_long"}{/if}
                                                {if $month == 8}{jrCore_lang module="jrEvent" id="48" default="August" assign="month_long"}{/if}
                                                {if $month == 9}{jrCore_lang module="jrEvent" id="49" default="September" assign="month_long"}{/if}
                                                {if $month == 10}{jrCore_lang module="jrEvent" id="50" default="October" assign="month_long"}{/if}
                                                {if $month == 11}{jrCore_lang module="jrEvent" id="51" default="November" assign="month_long"}{/if}
                                                {if $month == 12}{jrCore_lang module="jrEvent" id="52" default="December" assign="month_long"}{/if}

                                                <div class="center">
                                                    <h4>{$month_long} - {$year}</h4>
                                                </div>
                                                <div class="block_content" style="box-shadow: none;">
                                                    {jrEvent_calendar month=$month year=$year template="small_calendar.tpl" tpl_dir="jrEvent"}
                                                </div>

                                            </div>

                                        </div>

                                        <div class="col4 last">

                                            <div class="block">

                                                {if $month == '12'}
                                                    {assign var="nmonth" value="1"}
                                                    {math equation="x + y" x=$year y="1" assign="nyear"}
                                                {else}
                                                    {math equation="x + y" x=$month y="1" assign="nmonth"}
                                                {/if}

                                                {if $nmonth == 1}{jrCore_lang module="jrEvent" id="41" default="January" assign="nmonth_long"}{/if}
                                                {if $nmonth == 2}{jrCore_lang module="jrEvent" id="42" default="February" assign="nmonth_long"}{/if}
                                                {if $nmonth == 3}{jrCore_lang module="jrEvent" id="43" default="March" assign="nmonth_long"}{/if}
                                                {if $nmonth == 4}{jrCore_lang module="jrEvent" id="44" default="April" assign="nmonth_long"}{/if}
                                                {if $nmonth == 5}{jrCore_lang module="jrEvent" id="45" default="May" assign="nmonth_long"}{/if}
                                                {if $nmonth == 6}{jrCore_lang module="jrEvent" id="46" default="June" assign="nmonth_long"}{/if}
                                                {if $nmonth == 7}{jrCore_lang module="jrEvent" id="47" default="July" assign="nmonth_long"}{/if}
                                                {if $nmonth == 8}{jrCore_lang module="jrEvent" id="48" default="August" assign="nmonth_long"}{/if}
                                                {if $nmonth == 9}{jrCore_lang module="jrEvent" id="49" default="September" assign="nmonth_long"}{/if}
                                                {if $nmonth == 10}{jrCore_lang module="jrEvent" id="50" default="October" assign="nmonth_long"}{/if}
                                                {if $nmonth == 11}{jrCore_lang module="jrEvent" id="51" default="November" assign="nmonth_long"}{/if}
                                                {if $nmonth == 12}{jrCore_lang module="jrEvent" id="52" default="December" assign="nmonth_long"}{/if}

                                                <div class="center">
                                                    <h4>{$nmonth_long} - {if isset($nyear)}{$nyear}{else}{$year}{/if}</h4>
                                                </div>
                                                <div class="block_content" style="box-shadow: none;">
                                                    {if isset($nyear)}
                                                        {jrEvent_calendar month=$nmonth year=$nyear template="small_calendar.tpl" tpl_dir="jrEvent"}
                                                    {else}
                                                        {jrEvent_calendar month=$nmonth year=$year template="small_calendar.tpl" tpl_dir="jrEvent"}
                                                    {/if}
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>
                            </div>

                            <div class="body_1">
                                <div class="br-info capital" style="margin-bottom:10px;">
                                    {if !isset($_post.option) || $_post.option == 'by_newest'}
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;
                                    {else}
                                    <a href="{$jamroom_url}/events">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}</a>&nbsp;
                                    {/if}

                                    <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;

                                    {if isset($_post.option) && $_post.option == 'by_upcoming'}
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="68" default="upcoming"}
                                    {else}
                                    <a href="{$jamroom_url}/events/by_upcoming">{jrCore_lang skin=$_conf.jrCore_active_skin id="68" default="upcoming"}</a>
                                    {/if}

                                    {if jrCore_module_is_active('jrRating')}
                                    <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;

                                    {if isset($_post.option) && $_post.option == 'by_ratings'}
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="60" default="by rating"}
                                    {else}
                                    <a href="{$jamroom_url}/events/by_ratings">{jrCore_lang skin=$_conf.jrCore_active_skin id="60" default="by rating"}</a>
                                    {/if}
                                    {/if}
                                </div>
                                {if isset($_conf.jbTheBreak_require_images) && $_conf.jbTheBreak_require_images == 'on'}
                                    {if isset($search)}
                                    {jrCore_list module="jrEvent" order_by=$order_by search="`$search`" tpl_dir="jbTheBreak" template="events_row.tpl" require_image="event_image" pagebreak=$_conf.jbTheBreak_default_events_pagebreak page=$_post.p}
                                    {else}
                                    {jrCore_list module="jrEvent" order_by=$order_by tpl_dir="jbTheBreak" template="events_row.tpl" require_image="event_image" pagebreak=$_conf.jbTheBreak_default_events_pagebreak page=$_post.p}
                                    {/if}
                                {else}
                                    {if isset($search)}
                                    {jrCore_list module="jrEvent" order_by=$order_by search="`$search`" tpl_dir="jbTheBreak" template="events_row.tpl" pagebreak=$_conf.jbTheBreak_default_events_pagebreak page=$_post.p}
                                    {else}
                                    {jrCore_list module="jrEvent" order_by=$order_by tpl_dir="jbTheBreak" template="events_row.tpl" pagebreak=$_conf.jbTheBreak_default_events_pagebreak page=$_post.p}
                                    {/if}
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <div class="col3 last">
            <div class="body_1">
                {jrCore_include template="side_home.tpl"}
            </div>
        </div>

    </div>
</div>

{jrCore_include template="footer.tpl"}
