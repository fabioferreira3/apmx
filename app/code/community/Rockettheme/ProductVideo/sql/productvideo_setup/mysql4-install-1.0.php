<?php
/**
 * @version   1.5.0.2 April 10, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

$installer = $this;
$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->addAttribute('catalog_product', 'video_link', array(
        'backend'       => '',
        'source'        => '',
        'entity_model'	=> 'catalog/product',
        'label'         => 'Product Video',
		'note'          => 'YouTube: add the URL link from the Share section | Google Video: add the full embed code | Flowplayer (self hosted): add the video name, for example product_video.flv',
        'group'			=> 'Product Video',
        'input'         => 'text',
        'type'			=> 'text',
        'is_html_allowed_on_front' => true,
        'global'        => true,
        'visible'       => true,
        'required'      => false,
        'user_defined'  => false,
        'default'       => '',
        'visible_on_front' => false
    ));

$installer->endSetup();