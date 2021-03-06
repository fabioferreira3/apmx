<?php
/**
 * @version   1.7.0.1 May 8, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class Rockettheme_MageMenus_Model_Menutree
{

   	public function toOptionArray()
    {
        return array(
			array('value'=>'disabled', 'label'=>Mage::helper('magemenus')->__('No')),
			array('value'=>'normal', 'label'=>Mage::helper('magemenus')->__('Yes - as standard accordions')),
            array('value'=>'tree', 'label'=>Mage::helper('magemenus')->__('Yes - as single tree, under root category heading')),
        );
    }

 }