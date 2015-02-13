{jrCore_module_url module="jrUser" assign="murl"}
<script type="text/javascript">
$(document).ready(function(){
    $.get('{$jamroom_url}/{$murl}/online_status/{$type}/{$unique_id}/{$seconds}/{$template}/__ajax=1', function(res) { $('#{$id}').html(res); });
});
</script>
<div id="{$id}">{* output loads here *}</div>