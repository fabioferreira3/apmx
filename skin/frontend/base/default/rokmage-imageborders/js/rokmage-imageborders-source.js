/**
 * @version    1.7.0.0 May 5, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright   Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license    http://www.rockettheme.com/legal/license.php RocketTheme Proprietary Use License
 */

(function($) {
// Product Image Rounded Borders
$(document).ready(function(){
// Homepage Content Slider Images
$("#homepage-contentslider-container .product-image").append('<span class="round-xl"></span>'),
$("#homepage-contentslider-container .product-image").addClass("rounded_xl");

// Category Content Slider Images
$("#contentslider-container .product-image").append('<span class="round-l"></span>'),
$("#contentslider-container .product-image").addClass("rounded_l");
// YouTube Video Link Images
// This now gets added via php in rokmage-productvideo.phtml // $(".video_container a:first").append('<span class="round-l"></span>'),
// $(".video_container a:first").addClass("rounded_l");

// Category Product Grid Images
$("#products-grid-table.products-grid .product-image").append('<span class="round-m"></span>'),
$("#products-grid-table.products-grid .product-image").addClass("rounded_m");
// Category Product List Images
$(".products-list .product-image").append('<span class="round-m"></span>'),
$(".products-list .product-image").addClass("rounded_m");
// CategoryView Combines Product Grid/List Images
$(".product-image-col .product-image").append('<span class="round-m"></span>'),
$(".product-image-col .product-image").addClass("rounded_m");
// Modal Cart Images
$(".cart-scrollable .product-image").append('<span class="round-m"></span>'),
$(".cart-scrollable .product-image").addClass("rounded_m");
// Product Scroller Images
// This now gets added via php in rokmage-product-scroller.phtml // $(".scrollable-container .product-image").append('<span class="round-m"></span>'),
$(".scrollable-container .product-image").addClass("rounded_m");
// Review Page
$(".product-review .product-image").append('<span class="round-m"></span>'),
$(".product-review .product-image").addClass("rounded_m");
// Compare Page
$("#product_comparison .product-image").append('<span class="round-m"></span>'),
$("#product_comparison .product-image").addClass("rounded_m");

// Upsell Product Images
$("#upsell-product-table .product-image").append('<span class="round-s"></span>'),
$("#upsell-product-table .product-image").addClass("rounded_s");
// Crosssell Product Images
$(".crosssell .product-image").append('<span class="round-s"></span>'),
$(".crosssell .product-image").addClass("rounded_s");
// Wishlist Images
$("#wishlist-sidebar .product-image").append('<span class="round-s"></span>'),
$("#wishlist-sidebar .product-image").addClass("rounded_s");
// Cart Images
$("#shopping-cart-table.data-table tr td:nth-child(1) a").append('<span class="round-s"></span>'),
$("#shopping-cart-table.data-table tr td:nth-child(1) a").addClass("rounded_s");
// Related Items Images
$(".block-related .item .product-image").append('<span class="round-s"></span>'),
$(".block-related .item .product-image").addClass("rounded_s");
});
})(jQuery);
