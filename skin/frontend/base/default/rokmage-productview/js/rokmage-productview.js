/*!
 * Based on:
 * jQuery miniZoomPan 1.0
 * 2009 Gian Carlo Mingati
 * Version: 1.0 (18-JUNE-2009)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

jQuery.fn.miniZoomPan=function(settings){settings=jQuery.extend({sW:imgSmallWidth,sH:imgSmallHeight,lW:imgWidth,lH:imgHeight,frameColor:"#FFFFFF",position:"absolute",frameWidth:0},settings);return this.each(function(){var div=jQuery(this);div.css({width:settings.sW,position:settings.position,height:settings.sH,border:settings.frameWidth+"px solid "+settings.frameColor}).addClass("minizoompan");var ig=div.children();ig.css({position:"relative"});jQuery(window).bind("load",function(){ig.width(settings.sW);ig.height(settings.sH)});div.css({overflow:"hidden"});div.mousemove(function(e){var divWidth=div.width();var divHeight=div.height();var igW=ig.width();var igH=ig.height();var dOs=div.offset();var leftPan=(e.pageX-dOs.left)*(divWidth-igW)/(divWidth+settings.frameWidth*2);var topPan=(e.pageY-dOs.top)*(divHeight-igH)/(divHeight+settings.frameWidth*2);ig.css({left:leftPan,top:topPan})});div.toggle(function(){ig.animate({left:-settings.lW/2,top:-settings.lH/2,width:settings.lW,height:settings.lH}, 100);ig.css({cursor:"crosshair"});},function(){ig.animate({left:"0",top:"0",width:settings.sW,height:settings.sH}, 100);ig.css({cursor:"default"})});function swapImage(param,uri){param.load(function(){}).error(function(){alert("Image does not exist or its SRC is not correct.")}).attr('src',uri)}})};function switch_product_img(divName,totalImgs){for(var i=1;i<=totalImgs;i++){var showDivName='photo_'+i;var showObj=document.getElementById(showDivName);if(showDivName==divName)jQuery(showObj).fadeIn('normal');else jQuery(showObj).fadeOut('normal')}};jQuery(window).load(function() {jQuery("img#productImgDefault").fadeIn(200)});