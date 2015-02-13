<style>
    .CodeMirror {
        line-height: 1.2;
        width: 100%;
        margin: 0;
        padding: 0;
    }

    #view {
        max-width: 1108px;
    }
</style>
<div id=view><!-- difference view loads into here  --></div>

<form name="meta" id="meta">
<input type="hidden" name="template_body" id="template_body" value=""/>
{if isset($skin)}
<input type="hidden" name="skin" value="{$skin}"/>
{else}
<input type="hidden" name="module_url" value="{$module_url}"/>
{/if}
{if isset($template_id)}
<input type="hidden" name="template_id" value="{$template_id}"/>
{/if}
{if isset($template_name)}
<input type="hidden" name="template_name" value="{$template_name}"/>
{/if}

</form>

<script type="text/javascript">
    var code_left, code_right, dv;
        code_left = {$code_left};
        code_right = {$code_right};

    $(document).ready(function() {
        show_diff();
        dv.editor().setValue(code_left)
    });

    /**
     * initalize the difference panel
     */
    function show_diff() {
        if (code_left == null) {
            return;
        }
        var target = document.getElementById("view");
        target.innerHTML = "";
        dv = CodeMirror.MergeView(target, {
            value: '',
            orig: code_right,
            lineNumbers: true,
            mode: 'smarty',
            highlightDifferences: true
        });
    }

    /**
     * updates the textarea on save
     */
    function update_textarea() {
        var template_body = dv.editor().getValue();
        $('#template_body').val(template_body)
    }

</script>
