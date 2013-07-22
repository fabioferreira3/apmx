/**
 * @version    $Id: additionalscripts.js 47705 2012-01-18 21:55:02Z sam $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright   Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license    http://www.rockettheme.com/legal/license.php RocketTheme Proprietary Use License
 */

var $j = jQuery.noConflict();
var $dataajax = '.switchtodesktop, #logo a, li.home a, a.product-link, .product-name a, .product-shop .add-to-cart a, button #idsend2, button #idsend, .advancedsearch, a.top-link-checkout';
var $inlinebuttons = '.registered-users .buttons-set .ui-btn, .account-create .buttons-set .ui-btn, .forgot-password .buttons-set .ui-btn, #shopping-cart-table .first .ui-btn, #contactForm .ui-btn';
var $uibody = '.dashboard .box, .cart .totals';

// Get cookie for back button
var backbtn = $j.cookie('backbtn');
    
$j(document).ready( function() {

    // Check cookie, if back button active
    if (backbtn == 'active') {
        $j('.backbutton').addClass('active');
        $j('.searchbar,.backbutton').removeClass('first-time');
    } else {
        $j.cookie('backbtn', 'active', { expires: 1, path: '/' });
    };

    // Run scripts for next page
    $j('div.ui-page').live('pagebeforeshow', function(event, ui) {
        $j($dataajax).attr("data-ajax","false");
        // Check cookie, if back button active
        if (backbtn == 'active') {
            $j('.backbutton').addClass('active');
            $j('.searchbar,.backbutton').removeClass('first-time');
        } else {
            $j.cookie('backbtn', 'active', { expires: 1, path: '/' });
        };
        $j($dataajax).attr("data-ajax","false");
        $j($inlinebuttons).addClass("ui-btn-inline");
        $j($uibody).addClass("ui-body ui-body-b");
        $j('.page-title').addClass("ui-header ui-bar-a");
        $j('.page-title h1').addClass("ui-title");
        $j('ul.checkout-types li > div.ui-btn').addClass("ui-btn-up-b").removeClass("ui-btn-up-c");
        $j('#shopping-cart-table thead tr').addClass("ui-bar-a");
    });

    // Run scripts for previous page 
    $j('div.ui-page').live('pagehide', function(event, ui) {
        $j($dataajax).attr("data-ajax","false");
        // Check cookie, if back button active
        if (backbtn == 'active') {
            $j('.backbutton').addClass('active');
            $j('.searchbar,.backbutton').removeClass('first-time');
        } else {
            $j.cookie('backbtn', 'active', { expires: 1, path: '/' });
        };
        $j($dataajax).attr("data-ajax","false");
        $j($inlinebuttons).addClass("ui-btn-inline");
        $j($uibody).addClass("ui-body ui-body-b");
        $j('ul.checkout-types li > div.ui-btn').addClass("ui-btn-up-b").removeClass("ui-btn-up-c");
        $j('#shopping-cart-table thead tr').addClass("ui-bar-a");
    });

    $j($dataajax).attr("data-ajax","false");
    $j($inlinebuttons).addClass("ui-btn-inline");
    $j($uibody).addClass("ui-body ui-body-b");
    $j('ol.opc').addClass("onestep").removeClass("opc");
    $j('#shopping-cart-table thead tr').addClass("ui-bar-a");

});




