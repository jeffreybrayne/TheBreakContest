{if isset($_post.standalone)}
<!doctype html>
<html>
<head>
<script type="text/javascript" src="{jrCore_javascript_src}"></script>
</head>
<body>
{/if}

<script type="text/javascript">

    $(document).ready(function() {

        $.plot("#g{$unique_id}", [
        {foreach $_sets as $set}
           {$set}
        {/foreach} ]

        {if isset($options)}
            {$options}
        {/if}
        );

        {if $hoverable == 1}

            var id = "#g{$unique_id}";

            {if isset($function)}

                $(id).bind("plothover", {$function});

            {else}

                $(id).bind("plothover", function (event, pos, item) {
                    var xy = '#xyval';
                    var tu = '#t{$unique_id}';
                    if (item) {
                        {if isset($tooltip_format)}
                        var x = strftime('{$tooltip_format}', new Date(item.datapoint[0]));
                        {/if}
                        var y;
                        if (typeof item.datapoint[1] === 'number' && item.datapoint[1] % 1 === 0) {
                            y = item.datapoint[1];
                        }
                        else {
                            y = item.datapoint[1].toFixed(3);
                        }
                        if ($(xy).length == 0) {
                            $(tu).html(x +': '+ y).css({ top: item.pageY-38, left: item.pageX-30 }).fadeIn(150);
                        }
                        else {
                            $(xy).html(x + ': ' + y).fadeIn(150);
                        }
                    }
                    else {
                        if ($(xy).length == 0) {
                            $(tu).hide();
                        }
                        else {
                            $(xy).hide();
                        }
                    }
                });

            {/if}
        {/if}

    });

</script>

<div id="g{$unique_id}" class="graph-holder" style="width:{$width};height:{$height}"></div>
<div id="l{$unique_id}" class="graph-legend"></div>
<div id="t{$unique_id}" class="p5 notice graph-tooltip" style="position:absolute;display:none"></div>

{if isset($_post.standalone)}
</body>
</html>
{/if}
