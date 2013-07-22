<?php
/**
 * @version   1.6.2.0 March 14, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class Rockettheme_FeaturedProducts_Model_Featuredtype
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'category', 'label'=>Mage::helper('adminhtml')->__('Featured Category')),
            array('value'=>'attribute', 'label'=>Mage::helper('adminhtml')->__('Featured Attribute')),
			array('value'=>'list', 'label'=>Mage::helper('adminhtml')->__('Featured List')),
        );
    }

}
