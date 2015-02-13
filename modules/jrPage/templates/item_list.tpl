{if isset($_items)}
{foreach from=$_items item="item"}
  <div class="item">

      <div class="container">
          <div class="row">
              <div class="col12 last">
                  <div class="p5 pl10">
                      <h2><a href="{jrProfile_item_url module="jrPage" profile_url=$item.profile_url item_id=$item._item_id title=$item.page_title}">{$item.page_title}</a></h2><br>
                      <span class="normal">{$item.page_body|jrCore_format_string:$item.profile_quota_id|jrCore_strip_html|truncate:220}</span>
                  </div>
              </div>
          </div>

      </div>

  </div>
{/foreach}
{/if}
