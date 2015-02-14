{assign var="title" value="`$profile_name`: `$blog_title`"}
{jrCore_module_url module="jrBlog" assign="murl"}
<meta property="og:url" content="{$current_url|replace:"http:":"`$method`:"}" />
<meta property="og:type" content="website" />
<meta property="og:title" content="{$title|jrCore_entity_string}" />
{if isset($blog_image_size) && $blog_image_size > 100}
<meta property="og:image" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/image/blog_image/{$_item_id}/large/_v={$blog_image_time}" />
{/if}
<meta property="og:description" content="{$blog_text|jrCore_format_string:$profile_quota_id|jrCore_strip_html|jrCore_entity_string|truncate:200}" />
<meta property="og:see_also" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$profile_url}" />
<meta property="og:site_name" content="{$_conf.jrCore_system_name}" />
<meta property="og:updated_time" content="{$_updated}" />
