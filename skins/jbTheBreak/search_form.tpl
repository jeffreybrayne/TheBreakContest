{* Standard HTML search form *}
{assign var="form_name" value="jrSearch"}
<div style="white-space:nowrap">
    <form name="{$form_name}" action="{$jamroom_url}/search/results/{$jrSearch.module}/{$jrSearch.page}/{$jrSearch.pagebreak}" method="post" style="margin-bottom:0">
        <input type="text" name="search_string" value="{$jrSearch.value}" style="{$jrSearch.style}" class="{$jrSearch.class}" onfocus="if(this.value=='{$jrSearch.value}'){  this.value='';  }" onblur="if(this.value==''){  this.value='{$jrSearch.value}';  }"><br>
        <br>
        <input type="submit" class="form_button" value="{$jrSearch.submit_value|default:"search"}">
    </form>
</div>
