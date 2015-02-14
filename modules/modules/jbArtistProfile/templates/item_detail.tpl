{jrCore_module_url module="jbArtistProfile" assign="murl"}
<div class="block">

    <div class="title">
        <div class="block_config">
            {jrCore_item_detail_buttons module="jbArtistProfile" item=$item}
        </div>
        <h1>{$item.artistprofile_title}</h1>

        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$item.profile_url}/">{$item.profile_name}</a> &raquo;
            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jbArtistProfile" id="10" default="ArtistProfile"}</a> &raquo; {$_post._2|default:"ArtistProfile"}
        </div>
    </div>

    <div class="block_content">

        <div class="item">
            <div class="container">
                <div class="row">
                    <div class="col3">
                        <div class="block_image center">
                            {foreach from=$item item="v" key="k"}
                                {if (substr($v,0,6)) == 'image/'}
                                    {assign var="type" value=$k|substr:0:-5}
                                    {jrCore_module_function function="jrImage_display" module="jbArtistProfile" type=$type item_id=$item._item_id size="large" alt=$item.artistprofile_title width=false height=false class="iloutline img_scale"}
                                    <br>
                                {/if}
                            {/foreach}
                            {jrCore_module_function function="jrRating_form" type="star" module="jbArtistProfile" index="1" item_id=$item._item_id current=$item.artistprofile_rating_1_average_count|default:0 votes=$item.artistprofile_rating_1_count|default:0 }
                        </div>
                    </div>
                    <div class="col9 last">
                        <div class="p5">
                            <h2>{$item.artistprofile_title}</h2>
                            {foreach from=$item item="v" key="k"}
                                {assign var="m" value="ArtistProfile"}
                                {assign var="l" value=$m|strlen}
                                {if substr($k,0,$l) == "artistprofile"}
                                    <span class="info">{$k}:</span> <span class="info_c">{$v}</span><br>
                                {/if}
                            {/foreach}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {jrCore_item_detail_features module="jbArtistProfile" item=$item}
    </div>

</div>
