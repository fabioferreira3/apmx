<?php
/**
 * @version   1.7.0.0 May 5, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class Rockettheme_HomepageGrid_Model_Homepagegridfilter
{

    public function toOptionArray()
    {
        return array(
			array('value'=>'selectlist', 'label'=>Mage::helper('contentslider')->__('-- Select Specific Products From List Below --')),
			array('value'=>'all', 'label'=>Mage::helper('adminhtml')->__('Show All Products')),
            array('value'=>'featured', 'label'=>Mage::helper('adminhtml')->__('Featured Products')),
            array('value'=>'bestselling', 'label'=>Mage::helper('adminhtml')->__('Best Selling Products')),
			array('value'=>'recent', 'label'=>Mage::helper('adminhtml')->__('Recently Added Products')),
			array('value'=>'viewed', 'label'=>Mage::helper('adminhtml')->__('Most Viewed Products')),
			array('value'=>'rated', 'label'=>Mage::helper('adminhtml')->__('Highest Rated Products')),
			array('value'=>'specialprice', 'label'=>Mage::helper('adminhtml')->__('Special Price Products'))
        );
    }

}
