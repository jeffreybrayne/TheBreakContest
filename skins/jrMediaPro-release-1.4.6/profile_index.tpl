{* default index for profile *}

<div class="col8 last">
    {jrCore_include module="jrAction" template="item_index.tpl"}
    {if $_conf.jrMediaPro_profile_comments == 'on'}
        <br>
        <div class="block">
            <div class="title">
                <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="77" default="Comments"}</h2>
            </div>
        </div>
        {jrComment_form module="jrProfile" profile_id=$_profile_id item_id=$_item_id}
    {/if}
</div>
