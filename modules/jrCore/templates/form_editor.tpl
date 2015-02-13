{jrCore_module_url module="jrCore" assign="murl"}
tinymce.init({
    body_id: "{$form_editor_id}",
    content_css: "{$jamroom_url}/{$murl}/css/{$murl}/jrCore_tinymce.css?v={$_mods.jrCore.module_version}",
    toolbar_items_size : "small",
    element_format: "html",
    autoresize_bottom_margin: "3",
    keep_styles: false,
    theme: "{$theme}",
    selector: "textarea#{$form_editor_id}",
    relative_urls: false,
    remove_script_host: false,
    convert_fonts_to_spans: true,
    menubar: false,
    statusbar: false,
    paste_as_text: true,
    entity_encoding: "raw",
    height: "100%",
    image_advtab: true,
    plugins: "contextmenu,pagebreak,{if $jrsmiley}jrsmiley,{/if}{if $jrembed}jrembed,media{/if},image,autoresize,{if $table}table,{/if}link,code,fullscreen,textcolor,colorpicker",
    toolbar: ["formatselect |{if $strong} bold{/if}{if $em} italic{/if}{if $span} underline{/if} forecolor | {if $span || $div} alignleft{/if}{if $span || $div} aligncenter{/if}{if $span || $div} alignright{/if}{if $span || $div} alignjustify |{/if}{if $ul && $li} bullist numlist |{/if}{if $div} outdent indent |{/if} undo redo | link unlink anchor pagebreak{if $table} table{/if} | {if $hr} hr{/if}{if $sub && $sup} | sub sup {/if}{if $jrembed} | jrembed{/if}{if $jrsmiley} jrsmiley{/if} | code fullscreen"],
    contextmenu: "link image | cut copy paste"
});
