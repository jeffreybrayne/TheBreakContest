{if isset($_items)}
  {jrCore_module_url module="jrBlog" assign="murl"}
  {foreach from=$_items item="row"}
  <div style="display:table">
      <div style="display:table-cell">

          {if isset($row.blog_image_size) && $row.blog_image_size > 0}
              <a href="{$jamroom_url}/{$row.profile_url}/{$murl}/{$row._item_id}/{$row.blog_title_url}">{jrCore_module_function function="jrImage_display" module="jrBlog" type="blog_image" item_id=$row._item_id size="small" crop="auto" alt=$row.blog_title title=$row.blog_title class="iloutline iindex"}</a>
          {else}
              <a href="{$jamroom_url}/{$row.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$row._profile_id size="small" crop="auto" alt=$row.profile_name title=$row.profile_name class="iloutline iindex"}</a>
          {/if}

      </div>
      <div class="p5" style="display:table-cell;vertical-align:middle">
          <a href="{$jamroom_url}/{$row.profile_url}/{$murl}/{$row._item_id}/{$row.blog_title_url}" class="media_title">{$row.blog_title}</a>
      </div>
  </div>
  {/foreach}
{/if}