/**
 * Font Controller
 * For creating a font size changer interface with minimum effort
 * Copyright (c) 2009 Hafees (http://cool-javascripts.com)
 * License: Free to use, modify, distribute as long as this header is kept :)
 *
 */

var $j=jQuery.noConflict();
function fontSize(container,target,minSize,medSize,maxSize,FontSizeText,CookieName,minCaption,maxCaption)
{
    var medCaption="";
    text="<span class='rokmage-text-resize'>"+FontSizeText+"</span>";
    smallFontHtml="<a href='javascript:void(0);' class='smallFont' title='"+minCaption+"'>"+minCaption+"</a> ";
    largeFontHtml="<a href='javascript:void(0);' class='largeFont' title='"+maxCaption+"'>"+maxCaption+"</a> ";
    $j(container).html(text+smallFontHtml+largeFontHtml);
    if($j.cookie!=undefined){
        var cookie=target.replace(/[#. ]/g,'');
        var value=$j.cookie(CookieName);
        if(value!=null){
            $j(target).css('font-size',parseInt(value))
        }
    }
    $j(container+" .smallFont").click(function(){
        curSize=parseInt($j(target).css("font-size"));
        newSize=curSize-2;
        if(newSize>=minSize){
            $j(target).css('font-size',newSize);
			updatefontCookie(CookieName,newSize);
        }
        if(newSize<=minSize){
            $j(container+" .smallFont").addClass("sdisabled")
        }
        if(newSize<maxSize){
            $j(container+" .largeFont").removeClass("ldisabled")
        }     
    });
    $j(container+" .medFont").click(function(){
        $j(target).css('font-size',medSize);
        $j(container+" .smallFont").removeClass("sdisabled");
        $j(container+" .largeFont").removeClass("ldisabled");
        updatefontCookie(CookieName,medSize)
    });
    $j(container+" .largeFont").click(function(){
        curSize=parseInt($j(target).css("font-size"));
        newSize=curSize+2;
        if(newSize<=maxSize){
            $j(target).css('font-size',newSize);
			updatefontCookie(CookieName,newSize);
        }
        if(newSize>minSize){
            $j(container+" .smallFont").removeClass("sdisabled")
        }
        if(newSize>=maxSize){
            $j(container+" .largeFont").addClass("ldisabled")
        }
     });
    function updatefontCookie(target,size){
        if($j.cookie!=undefined){var cookie=target.replace(/[#. ]/g,'');$j.cookie(cookie,size)}
    }
};
/******************************************************************************************************/
/**
 * Cookie plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 */
jQuery.cookie=function(name,value,options){if(typeof value!='undefined'){options=options||{};if(value===null){value='';options.expires=-1}var expires='';if(options.expires&&(typeof options.expires=='number'||options.expires.toUTCString)){var date;if(typeof options.expires=='number'){date=new Date();date.setTime(date.getTime()+(options.expires*24*60*60*1000))}else{date=options.expires}expires='; expires='+date.toUTCString()}var path=options.path?'; path='+(options.path):'';var domain=options.domain?'; domain='+(options.domain):'';var secure=options.secure?'; secure':'';document.cookie=[name,'=',encodeURIComponent(value),expires,path,domain,secure].join('')}else{var cookieValue=null;if(document.cookie&&document.cookie!=''){var cookies=document.cookie.split(';');for(var i=0;i<cookies.length;i++){var cookie=jQuery.trim(cookies[i]);if(cookie.substring(0,name.length+1)==(name+'=')){cookieValue=decodeURIComponent(cookie.substring(name.length+1));break}}}return cookieValue}};