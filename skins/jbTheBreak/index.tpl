{jrCore_include template="header.tpl"}
{if jrUser_is_logged_in()}
	{jrCore_include template="index_contest.tpl"}
{else}
	{jrCore_include template="index_content.tpl"}
{/if}
{jrCore_include template="footer.tpl"}