<body style="background:#000;">

<style type="text/css">
img.bg {
    min-height: 100%;
    min-width: 1024px;
    width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
}
.wrap {
    position: absolute;
    top: 50px;
    left: 50px;
    width: 600px;
}
.message {
    margin: 12px;
    font-size: 18px;
    color: #FFF;
    text-shadow: 1px 1px 1px #000;
}
.content {
    zoom: 1;
    margin: 12px;
    opacity: 1;
    border-radius: 12px;
}
.content h1 {
    font-size: 48px;
    padding: 0;
    color: #FFF;
    text-align: center;
    text-shadow: 1px 1px 1px #000;
}
.login_link {
    display: inline-block;
    margin: 36px 0 0 12px;
    font-size: 14px;
    text-shadow: 1px 1px 1px #000;
}
.login_link a {
    color: #FFF;
}
.login_link a:hover {
    text-decoration: none;
}

@media screen and (max-width: 1024px) {
    img.bg {
        left: 50%;
        margin-left: -512px;
    }
    .wrap {
        top: 0;
        left: 0;
        width: 100%;
    }
    .message {
        font-size: 14px;
    }
    .content h1 {
        font-size: 28px;
    }
}
</style>

{jrCore_image module="jrUser" image="background.jpg" class="bg" alt=""}

<div class="wrap">

    <div class="content">
        <h1>{$_conf.jrCore_system_name}</h1>
    </div>

    <div class="message">
        {jrCore_lang module="jrUser" id=109 default="This site is private and only visible to logged in users."}
    </div>

    <div class="login_link">
        {jrCore_module_url module="jrUser" assign="url"}
        <a href="{$jamroom_url}/{$url}/login">{jrCore_lang module="jrUser" id=110 default="Click Here to Log In"}</a>
        {if $show_signup == 'yes'}
            <br><a href="{$jamroom_url}/{$url}/signup">{jrCore_lang module="jrUser" id=111 default="Click Here to Create an Account"}</a>
        {/if}
    </div>

</div>

</body>
</html>