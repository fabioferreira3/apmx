/**
 * @version    1.7.0.1 May 8, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright   Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license    http://www.rockettheme.com/legal/license.php RocketTheme Proprietary Use License
 */

var $j=jQuery.noConflict();$j(document).ready(function(){if($j('ul#magemenu-top > li').hasClass('active')){var default_left=Math.round($j('ul#magemenu-top > li.active').position().left+$j('ul#magemenu-top').position().left);var default_width=$j('ul#magemenu-top > li.active').width();$j('#box').css({left:default_left});$j('#box .head').css({width:default_width});$j('ul#magemenu-top > li').hover(function(){left=Math.round($j(this).position().left+$j('ul#magemenu-top').position().left);width=$j(this).width();$j('#box').stop(false,true).animate({left:left},{duration:200});$j('#box .head').stop(false,true).animate({width:width},{duration:200})}).click(function(){$j('ul#magemenu-top > li').removeClass('active');$j(this).addClass('active')});$j('ul#magemenu-top').mouseleave(function(){default_left=Math.round($j('ul#magemenu-top > li.active').position().left+$j('ul#magemenu-top').position().left);default_width=$j('ul#magemenu-top > li.active').width();$j('#box').stop(false,true).animate({left:default_left},{duration:200});$j('#box .head').stop(false,true).animate({width:default_width},{duration:200})})}});