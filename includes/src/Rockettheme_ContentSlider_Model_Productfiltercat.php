<?php
/**
 * @version   1.7.0.0 May 5, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class Rockettheme_ContentSlider_Model_Productfiltercat
{

    public function toOptionArray()
    {
        return array(
			array('value'=>'all', 'label'=>Mage::helper('contentslider')->__('Show All Products')),
            array('value'=>'featured', 'label'=>Mage::helper('contentslider')->__('Featured Products')),
            array('value'=>'bestselling', 'label'=>Mage::helper('contentslider')->__('Best Selling Products')),
			array('value'=>'recent', 'label'=>Mage::helper('contentslider')->__('Recently Added Products')),
			array('value'=>'viewed', 'label'=>Mage::helper('contentslider')->__('Most Viewed Products')),
			array('value'=>'rated', 'label'=>Mage::helper('contentslider')->__('Highest Rated Products')),
			array('value'=>'specialprice', 'label'=>Mage::helper('contentslider')->__('Special Price Products'))
        );
    }

}
