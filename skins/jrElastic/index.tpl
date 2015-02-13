{jrCore_include template="header.tpl"}

<div class="container">

    <div class="row">

        <div class="col3">

            {* Newest Profiles Block *}
            <div class="block">
                <div class="title">
                    <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="12" default="profiles"}</h2>
                    {jrCore_module_url module="jrProfile" assign="profile_murl"}
                    <div class="title-more"><a href="{$jamroom_url}/{$profile_murl}">{jrCore_lang skin=$_conf.jrCore_active_skin id="22" default="all"}</a></div>
                </div>
                <div class="block_content">
                    <div class="item">
                        {jrCore_list module="jrProfile" order_by="_created desc" search1="profile_active = 1" template="index_list_profiles.tpl" limit="5" require_image="profile_image"}
                    </div>
                </div>
            </div>


            {* Newest Audio Block *}
            {if jrCore_module_is_active('jrAudio')}
                <div class="block">
                    <div class="title">
                        <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="13" default="audio"}</h2>
                        {jrCore_module_url module="jrAudio" assign="audio_murl"}
                        <div class="title-more"><a href="{$jamroom_url}/{$audio_murl}">{jrCore_lang skin=$_conf.jrCore_active_skin id="22" default="all"}</a></div>
                    </div>
                    <div class="block_content">
                        <div class="item">
                            {jrCore_list module="jrAudio" order_by="_created desc" template="index_list_audio.tpl" limit="5" require_image="audio_image"}
                        </div>
                    </div>
                </div>
            {/if}


            {* Newest Groups Block *}
            {if jrCore_module_is_active('jrGroup')}
                <div class="block">
                    <div class="title">
                        <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="61" default="groups"}</h2>
                        {jrCore_module_url module="jrGroup" assign="group_murl"}
                        <div class="title-more"><a href="{$jamroom_url}/{$group_murl}">{jrCore_lang skin=$_conf.jrCore_active_skin id="22" default="all"}</a></div>
                    </div>
                    <div class="block_content">
                        <div class="item">
                            {jrCore_list module="jrGroup" order_by="_created desc" template="index_list_group.tpl" limit="5" require_image="group_image"}
                        </div>
                    </div>
                </div>
            {/if}


        </div>

        <div class="col6">

            <script type="text/javascript">
                $(function () {
                    $("#slider1").responsiveSlides({
                        auto: true,          // Boolean: Animate automatically, true or false
                        speed: 400,          // Integer: Speed of the transition, in milliseconds
                        timeout: 4000,       // Integer: Time between slide transitions, in milliseconds
                        pager: true,         // Boolean: Show pager, true or false
                        random: true,        // Boolean: Randomize the order of the slides, true or false
                        pause: true,         // Boolean: Pause on hover, true or false
                        maxwidth: 560,       // Integer: Max-width of the slideshow, in pixels
                        namespace: "rslides" // String: change the default namespace used
                    });
                });
            </script>
            <div class="block">


                {* Featured Profile Slider *}
                <div class="title"><h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="21" default="featured"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="12" default="profiles"}</h2></div>
                <div class="block_content">
                    <div id="swrapper" style="padding-top:10px;">
                        <div class="callbacks_container">
                            <div class="ioutline">
                                <ul id="slider1" class="rslides callbacks">
                                    {if isset($_conf.jrElastic_profile_ids) && strlen($_conf.jrElastic_profile_ids) > 0}
                                        {jrCore_list module="jrProfile" order_by="_created desc" limit="10" search1="_profile_id in `$_conf.jrElastic_profile_ids`" search2="profile_active = 1" template="index_featured_slider.tpl" require_image="profile_image"}
                                    {else}
                                        {jrCore_list module="jrProfile" order_by="_created desc" limit="10" search1="profile_active = 1" template="index_featured_slider.tpl" require_image="profile_image"}
                                    {/if}
                                </ul>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>


                {* Newest Images Block *}
                {if jrCore_module_is_active('jrGallery')}
                    <div style="margin-top:7px;">
                        <div class="title">
                            <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="59" default="images"}</h2>
                            {jrCore_module_url module="jrGallery" assign="gallery_murl"}
                            <div class="title-more"><a href="{$jamroom_url}/{$gallery_murl}">{jrCore_lang skin=$_conf.jrCore_active_skin id="22" default="all"}</a></div>
                        </div>
                        <div class="block_content">
                            <div class="item">
                                {jrCore_list module="jrGallery" order_by="_created desc" limit="12" template="index_list_gallery.tpl"}
                            </div>
                        </div>
                    </div>
                {/if}


            </div>

        </div>

        <div class="col3 last">


            {* Newest Blogs Block *}
            <div class="block">{jrCore_module_url module="jrBlog" assign="blog_murl"}
                <div class="title">
                    <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="42" default="Blogs"}</h2>
                    {jrCore_module_url module="jrBlog" assign="blog_murl"}
                    <div class="title-more"><a href="{$jamroom_url}/{$blog_murl}">{jrCore_lang skin=$_conf.jrCore_active_skin id="22" default="all"}</a></div>
                </div>
                <div class="block_content">
                    <div class="item">
                        {jrCore_list module="jrBlog" order_by="_created desc" template="index_list_blogs.tpl" limit="5"}
                    </div>
                </div>
            </div>


            {* Newest Videos Block *}
            {if jrCore_module_is_active('jrVideo')}
                <div class="block">
                    <div class="title">
                        <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="14" default="videos"}</h2>
                        {jrCore_module_url module="jrVideo" assign="video_murl"}
                        <div class="title-more"><a href="{$jamroom_url}/{$video_murl}">{jrCore_lang skin=$_conf.jrCore_active_skin id="22" default="all"}</a></div>
                    </div>
                    <div class="block_content">
                        <div class="item">
                            {jrCore_list module="jrVideo" order_by="_created desc" template="index_list_videos.tpl" limit="5" require_image="video_image"}
                        </div>
                    </div>
                </div>
            {/if}


            {* Newest Discussions Block *}
            {if jrCore_module_is_active('jrGroupDiscuss')}
                <div class="block">
                    <div class="title">
                        <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="62" default="discussions"}</h2>
                        {jrCore_module_url module="jrGroupDiscuss" assign="discuss_murl"}
                        <div class="title-more"><a href="{$jamroom_url}/{$discuss_murl}">{jrCore_lang skin=$_conf.jrCore_active_skin id="22" default="all"}</a></div>
                    </div>
                    <div class="block_content">
                        <div class="item">
                            {jrCore_list module="jrGroupDiscuss" order_by="_created desc" template="index_list_discuss.tpl" limit="5"}
                        </div>
                    </div>
                </div>
            {/if}


        </div>

    </div>

</div>

{jrCore_include template="footer.tpl"}

