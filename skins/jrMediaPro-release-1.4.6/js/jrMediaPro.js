/**
 * pjpopwin() is a generic popup window creator
 */
function pjpopwin(mypage,myname,w,h,scroll)
{
    LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
    TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
    settings = 'height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',resizable';
    win = window.open(mypage,myname,settings)
    if (win.opener == null) {
        win.opener = self;
    }
    win.focus();
}

function jrLoad(id,url,round_id,round_num) {
    if (typeof url == "undefined") {
        return false;
    }
    if (url == 'blank') {
        $(id).hide();
    }
    else if (id == '#hidden') {
        $(id).hide();
        $(id).load(url);
    }
    else {
        if (id == '#rank') {
            var t = $('#rank').height();
            if (t == null) {
                $('#main').html('<div class="inner"><div id="rank"></div></div>');
            }
        }
        var h = $(id).height();
        if (h > 150) {
            $(id).height(h);
        }
        $(id).fadeTo(100,0.5,function() {
            $(id).html('<div style="text-align:center;padding:20px;margin:0 auto;"><img src="'+ core_system_url +'/skins/jrMediaPro/img/loading.gif" style="margin:15px;"><br>Loading...</div>');
            $(id).load(url,function() {
                var l = $(id).text();
                if (l.length < 1 && id != '#player') {
                    $(id).html('');
                }
                if (h > 150) {
                    $(id).height('100%');
                }
                if (round_id && round_num > 0) {
                    $(id).fadeTo(100,1.00,function() {
                        if (jQuery.browser.msie) {
                            this.style.removeAttribute('filter');
                        }
                        $(round_id).corner(round_num +'px');
                    });
                }
                else {
                    $(id).fadeTo(100,1,function() {
                        if (jQuery.browser.msie) {
                            this.style.removeAttribute('filter');
                        }
                    });
                }
            })
        });
    }
}

// SIDE STATS AND ONLINE TABS
function jrSetActive(id,no_remove) {
    if (typeof no_remove == "undefined") {
        $('.p_choice').removeClass('p_choice_active');
    }
    $(id).addClass('p_choice_active');
}

$(document).ready(function(){
// Flexslider Animation and Navigation settings
    $('#carousel').flexslider({
        animation: "slide",             //String: Select your animation type, "fade" or "slide"
        controlNav: false,              //Boolean: Create navigation for paging control of each slide? Note: Leave true for manualControls usage
        animationLoop: false,           //Boolean: Should the animation loop? If false, directionNav will received "disable" classes at either end
        slideshow: false,               //Boolean: Animate slider automatically
        itemWidth: 150,                 //Integer: thumbnail width
        itemMargin: 5,                  //Integer: Thumbnail margin
        mousewheel: false,              //{UPDATED} Boolean: Requires jquery.mousewheel.js (https://github.com/brandonaaron/jquery-mousewheel) - Allows slider navigating via mousewheel
        asNavFor: '#slider'             //String: Slider ID
    });

    $('#slider').flexslider({
        animation: "slide",             //String: Select your animation type, "fade" or "slide"
        easing: "swing",                //{NEW} String: Determines the easing method used in jQuery transitions. jQuery easing plugin is supported!
        controlNav: false,              //Boolean: Create navigation for paging control of each slide? Note: Leave true for manualControls usage
        animationLoop: false,           //Boolean: Should the animation loop? If false, directionNav will received "disable" classes at either end
        slideshow: true,                //Boolean: Animate slider automatically
        slideshowSpeed: 7000,           //Integer: Set the speed of the slideshow cycling, in milliseconds
        animationSpeed: 800,            //Integer: Set the speed of animations, in milliseconds
        pauseOnAction: true,            //Boolean: Pause the slideshow when interacting with control elements, highly recommended.
        pauseOnHover: true,             //Boolean: Pause the slideshow when hovering over slider, then resume when no longer hovering
        mousewheel: false,              //{UPDATED} Boolean: Requires jquery.mousewheel.js (https://github.com/brandonaaron/jquery-mousewheel) - Allows slider navigating via mousewheel
        touch: false,                   //{NEW} Boolean: Allow touch swipe navigation of the slider on touch-enabled devices
        sync: "#carousel",              //String: Carousel ID
        start: function(slider){
            $('body').removeClass('loading');
        }
    });
// Toggle Flex Slider
    $('#fadeout-carousel').click(function() {
        $('.toggle-carousel').toggle(1000);
    });

// Scroll To Top Function
    $(window).scroll(function(){
        if ($(this).scrollTop() > 100) {
            $('.scrollup').fadeIn();
        } else {
            $('.scrollup').fadeOut();
        }
    });

    $('.scrollup').click(function(){
        $("html, body").animate({ scrollTop: 0 }, 600);
        return false;
    });
});
