/**
 * @version    1.7.0.0 May 15, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright   Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license    http://www.rockettheme.com/legal/license.php RocketTheme Proprietary Use License
 */

var $j = jQuery.noConflict();
$j(document).ready(function(){
// Add styling to forms etc
$j(".form-language select, select#select-store").addClass("styled");
//$j("input.poll_vote").removeClass("poll_vote radio").addClass("styled");
//$j("input:checkbox").removeClass("checkbox related-checkbox").addClass("styled");
// Remove language bar styling for IE6
if (jQuery.browser.msie) {
                        if(parseInt(jQuery.browser.version) == 6) {
                            $j(".form-language select").removeClass("styled");
                    }};
//Exclude our Search Box
$j("#search").removeClass("input-text");

// Style our input boxes
$j("input.input-text").wrap("<div class='input-wrap'></div>").before("<span class='input-left'></span>");
$j("input.input-text").addClass("inputstyled");

//Exclude hidden form elements
$j("input[title=State/Province]:hidden").prev("span.input-left").css("display", "none");

});