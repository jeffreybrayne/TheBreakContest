{* See if our blog module is enabled *}
{jrCore_module_url module="jrBlog" assign="murl"}
<a href="{$jamroom_url}/{$murl}/feed/1" target="_blank">{jrCore_image module="jrBlog" image="feed.png" alt="RSS Feed" title="RSS Feed" style="vertical-align: top;"}&nbsp;<span class="capital">{jrCore_lang skin=$_conf.jrCore_active_skin id="135" default="Site Blogs"}&nbsp;-&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="133" default="Subscribe"}</span></a><br>
