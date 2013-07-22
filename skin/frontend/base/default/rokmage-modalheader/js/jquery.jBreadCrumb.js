/**
 * @author Jason Roy for CompareNetworks Inc.
 * Thanks to mikejbond for suggested updates
 *
 * Version 1.1
 * Copyright (c) 2009 CompareNetworks Inc.
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php
 *
 */
(function($){var _options={};var _container={};var _breadCrumbElements={};var _autoIntervalArray=[];var _easingEquation;jQuery.fn.jBreadCrumb=function(options){_options=$.extend({},$.fn.jBreadCrumb.defaults,options);return this.each(function(){_container=$(this);setupBreadCrumb()})};function setupBreadCrumb(){if(typeof(jQuery.easing)=='object'){_easingEquation='easeOutQuad'}else{_easingEquation='swing'}_breadCrumbElements=jQuery(_container).find('li');jQuery(_container).find('ul').wrap('<div style="overflow:hidden; position:relative;  width: '+jQuery(_container).css("width")+';"><div>');jQuery(_container).find('ul').width(5000);if(_breadCrumbElements.length>0){jQuery(_breadCrumbElements[_breadCrumbElements.length-1]).addClass('last');jQuery(_breadCrumbElements[0]).addClass('first');if(_breadCrumbElements.length>_options.minimumCompressionElements){compressBreadCrumb()}}};function compressBreadCrumb(){var finalElement=jQuery(_breadCrumbElements[_breadCrumbElements.length-1]);if(jQuery(finalElement).width()>_options.maxFinalElementLength){if(_options.beginingElementsToLeaveOpen>0){_options.beginingElementsToLeaveOpen--}if(_options.endElementsToLeaveOpen>0){_options.endElementsToLeaveOpen--}}if(jQuery(finalElement).width()<_options.maxFinalElementLength&&jQuery(finalElement).width()>_options.minFinalElementLength){if(_options.beginingElementsToLeaveOpen>0){_options.beginingElementsToLeaveOpen--}}var itemsToRemove=_breadCrumbElements.length-1-_options.endElementsToLeaveOpen;jQuery(_breadCrumbElements[_breadCrumbElements.length-1]).css({background:'none'});$(_breadCrumbElements).each(function(i,listElement){if(i>_options.beginingElementsToLeaveOpen&&i<itemsToRemove){jQuery(listElement).find('a').wrap('<span></span>').width(jQuery(listElement).find('a').width()+10);jQuery(listElement).append(jQuery('<div class="'+_options.overlayClass+'"></div>').css({display:'block'})).css({background:'none'});if(isIE6OrLess()){fixPNG(jQuery(listElement).find('.'+_options.overlayClass).css({width:'20px',right:"-1px"}))}var options={id:i,width:jQuery(listElement).width(),listElement:jQuery(listElement).find('span'),isAnimating:false,element:jQuery(listElement).find('span')};jQuery(listElement).bind('mouseover',options,expandBreadCrumb).bind('mouseout',options,shrinkBreadCrumb);jQuery(listElement).find('a').unbind('mouseover',expandBreadCrumb).unbind('mouseout',shrinkBreadCrumb);listElement.autoInterval=setInterval(function(){clearInterval(listElement.autoInterval);jQuery(listElement).find('span').animate({width:_options.previewWidth},_options.timeInitialCollapse,_options.easing)},(150*(i-2)))}})};function expandBreadCrumb(e){var elementID=e.data.id;var originalWidth=e.data.width;jQuery(e.data.element).stop();jQuery(e.data.element).animate({width:originalWidth},{duration:_options.timeExpansionAnimation,easing:_options.easing,queue:false});return false};function shrinkBreadCrumb(e){var elementID=e.data.id;jQuery(e.data.element).stop();jQuery(e.data.element).animate({width:_options.previewWidth},{duration:_options.timeCompressionAnimation,easing:_options.easing,queue:false});return false};function isIE6OrLess(){var isIE6=$.browser.msie&&/MSIE\s(5\.5|6\.)/.test(navigator.userAgent);return isIE6};function fixPNG(element){var image;if(jQuery(element).is('img')){image=jQuery(element).attr('src')}else{image=$(element).css('backgroundImage');image.match(/^url\(["']?(.*\.png)["']?\)$/i);image=RegExp.$1}$(element).css({'backgroundImage':'none','filter':"progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true, sizingMethod=scale, src='"+image+"')"})};jQuery.fn.jBreadCrumb.defaults={maxFinalElementLength:400,minFinalElementLength:200,minimumCompressionElements:4,endElementsToLeaveOpen:1,beginingElementsToLeaveOpen:1,timeExpansionAnimation:400,timeCompressionAnimation:500,timeInitialCollapse:400,easing:_easingEquation,overlayClass:'chevronOverlay',previewWidth:25}})(jQuery);

jQuery(document).ready(function(){	
    jQuery(".breadcrumbs").addClass("rok-breadcrumbs").jBreadCrumb();

    // Get menu items
    var title = jQuery.trim(document.title);
    var title2 = title.replace(/\s+/g,'').replace('"','');
    var cmsalt = "li[id='" + title2 + "']";

    // Add top nav parent to breadcrumbs
 
    if (jQuery(cmsalt).hasClass("active")) {
        var currparent = jQuery(cmsalt).parents("ul").parents("li.level0").attr("id");
        var link = jQuery("li[id='" + currparent + "'] a").attr('href');
        var linktitle = jQuery("li[id='" + currparent + "'] a span").html();
        if ( link == null) { } else {
            jQuery(".cms-page-view div.rok-breadcrumbs li.home").after('<li class="cms_page"><a href="' + link + '">' + linktitle + '</a></li>'); 
        }
    }
    
    // Add side nav parent to breadcrumbs
    var topnav = jQuery("li[id='" + title + "']");
    var elem = jQuery("li[id='clone-" + title + "'] a");
    if (topnav.hasClass("active")) { // If topnav showing item as active, do nothing, as top nav will handle it
    } else {
        if (elem.hasClass("current")) {
            var currparent = parents.prev("div").prev("a.mageside-menu-heading");
            var link = currparent.attr('href');
            var text = jQuery(currparent).html();
            var textinner = jQuery(text).html();
            if ( link == null) { } else {
                jQuery(".cms-page-view div.rok-breadcrumbs li.home").after('<li class="cms_page"><a href="' + link + '">' + textinner + '</a></li>');
            }
        }
    }
    
});


