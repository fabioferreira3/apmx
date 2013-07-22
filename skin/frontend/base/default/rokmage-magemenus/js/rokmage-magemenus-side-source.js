/**
 * @version    1.7.0.1 May 8, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright   Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license    http://www.rockettheme.com/legal/license.php RocketTheme Proprietary Use License
 */

(function($) {

$(document).ready(function(){

    $(".mageside-menu-toggle-container").hide();

    $(".mageside-menu-toggle-button").hover(function(){
		$(this).prev(".mageside-menu-heading").addClass("hover");
		}, function () {
		$(this).prev(".mageside-menu-heading").removeClass("hover");
    });

	$(".mageside-menu-toggle-button").toggle(function(){
                $(this).next(".mageside-menu-toggle-container").slideDown(400);
		$(this).addClass("active");
		$(this).prev(".mageside-menu-heading").addClass("active");
		}, function () {
                $(this).next(".mageside-menu-toggle-container").slideUp(400);
		$(this).removeClass("active");
                $(this).prev(".mageside-menu-heading").animate({pause: 1}, 400, function(){$(this).removeClass("active");});
	});

// Set current active link states
var title = $.trim(document.title).replace(/\s+/g,'').replace('"','');
var cmsalt = "a[id='clone-" + title + "']";
var parents = $("li[id='clone-" + title + "']").parents("ul").parents("div.mageside-menu-toggle-container");
$(cmsalt).addClass("activecurrent active").next("div.mageside-menu-toggle-button").removeClass("mageside-menu-toggle-button").addClass("mageside-menu-toggle-button-current").next("div.mageside-menu-toggle-container").css("display","block");
$("li[id='clone-" + title + "'] a").addClass("current");
parents.addClass("active");
parents.prev("div.mageside-menu-toggle-button").removeClass("mageside-menu-toggle-button").addClass("mageside-menu-toggle-button-current");
parents.prev("div").prev("a.mageside-menu-heading").addClass("activecurrent active");
$('.contacts-index-index a#clone-Contact').addClass("active");
});
})(jQuery);