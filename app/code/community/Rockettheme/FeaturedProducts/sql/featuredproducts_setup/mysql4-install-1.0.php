<?php
/**
 * @version   1.6.2.0 March 14, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

$installer = $this;
$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->addAttribute('catalog_product', 'featured', array(
        'backend'       => '',
        'source'        => '',
        'entity_model'	=> 'catalog/product',
        'label'         => 'Set as a featured product',
        'group'			=> 'Featured Product',
        'input'         => 'select',
		'type'          => 'text',
		'source'        => 'eav/entity_attribute_source_boolean',
        'is_html_allowed_on_front' => true,
        'global'        => true,
        'visible'       => true,
        'required'      => false,
        'user_defined'  => false,
        'default'       => '',
        'visible_on_front' => false
    ));

$installer->endSetup();


