{* Standard HTML recommend form *}
{assign var="form_name" value="jrRecommend"}
<div class="center">
    <form name="{$form_name}" action="{$jamroom_url}/recommend/results/{$jrRecommend.page}/20" method="post" style="margin-bottom:0">
        <input type="text" name="recommend_string" value="{$jrRecommend.value}" style="{$jrRecommend.style}" class="{$jrRecommend.class}" onfocus="if(this.value=='{$jrRecommend.value}'){  this.value='';  }" onblur="if(this.value==''){  this.value='{$jrRecommend.value}';  }"><br>
        <br><input type="submit" class="form_button" value="{$jrRecommend.submit_value|default:"find"}">
    </form>
</div>
