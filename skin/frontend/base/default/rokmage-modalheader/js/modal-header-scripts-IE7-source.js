/**
 * @version    1.7.0.0 May 5, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright   Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license    http://www.rockettheme.com/legal/license.php RocketTheme Proprietary Use License
 */

// ** RokMageModal **

 (function($){
    $.fn.extend({
        
        rokmagemodal: function(options) {

       var defaults = {
                                rokmagemodalcontainer: "div.modalcontent",
								overlayopacity: 0.2,
								overlayinspeed: 300,
								modalpauseb4entry: 200,
								modalentryspeed: 550,
								modalexitspeed: 350,
								pauseb4overlayfadeout: 500,
								overlayoutspeed: 200                       
			};
			var options = $.extend(defaults, options);
            return this.each(function() {
                var o = options;
				// Pause Function
				$.fn.pause = function(duration) {
				$(this).animate({ dummy: 1 }, duration);
				return this;
				};
                // Add our click OPEN event
                $(this).click(function (e) {
                    e.preventDefault();
                    // Add our overlay div
                    $('body').append('<div id="overlay" />');
                    // Fade in overlay
                    $('#overlay').css({"display":"block","opacity":"0"}).animate({opacity: o.overlayopacity}, o.overlayinspeed),
                    // Animate our modal window into view
                    $(o.rokmagemodalcontainer).css({"top": "50%"}).pause(o.modalpauseb4entry).fadeIn(200),
                    // Add our close image
                    $(o.rokmagemodalcontainer).append('<div class="modal-close" title="Close window" />');
                    // Add our click CLOSE event
                    $('#overlay, div.modal-close').click(function () {
                        //Animate our modal window out of view
                        $(o.rokmagemodalcontainer).fadeOut(200),
                        // Fade out and remove our overlay
                        $('#overlay').pause(o.pauseb4overlayfadeout).fadeOut(o.overlayoutspeed, function () { 
                            $(this).remove(); $('div.modal-close').remove();
                            // For video
                            var video = $("#video").clone();
                            $("#video").remove();
                            $("body").append(video);
                        });
                    });
                });
            });
        }
    });
})(jQuery);

jQuery(document).ready(function(){
// Change the log out icon
jQuery('a[title="Log Out"]').addClass("logout");
// Move modal windows to end of page
login = jQuery("#rokmage-login-content").clone();
jQuery("#rokmage-login-content").remove();
jQuery("body").append(login);
cart = jQuery("#rokmage-header-cart").clone();
jQuery("#rokmage-header-cart").remove();
jQuery("body").append(cart);
});

// Build spans function
$j.fn.wrapStart = function (numWords) { 
    var node = this.contents().filter(function () { 
            return this.nodeType == 3 
        }).first(),
        text = $j.trim(node.text()),
        first = text.split(" ", numWords).join(" ");

    if (!node.length)
        return;

    node[0].nodeValue = text.slice(first.length);
    node.before('<span class="color">' + first + '</span>');
};