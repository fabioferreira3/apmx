<?php
/**
 * @version   1.6.2.0 March 14, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class Rockettheme_FooterBlock_Model_Footerblockcolumncontent
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'Best Selling Products', 'label'=>Mage::helper('adminhtml')->__('Best-Selling List')),
            array('value'=>'Our Featured Products', 'label'=>Mage::helper('adminhtml')->__('Featured Products List')),
			array('value'=>'Recently Added Products', 'label'=>Mage::helper('adminhtml')->__('Recently Added List')),
			array('value'=>'Most Viewed Products', 'label'=>Mage::helper('adminhtml')->__('Most Viewed Products')),
			array('value'=>'Highest Rated Products', 'label'=>Mage::helper('adminhtml')->__('Highest Rated Products')),
			array('value'=>'Special Price Products', 'label'=>Mage::helper('adminhtml')->__('Special Price Products')),
			array('value'=>'text', 'label'=>Mage::helper('adminhtml')->__('Custom Text (input below)')),
        );
    }

}
