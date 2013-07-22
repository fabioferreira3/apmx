<?php
/**
 * @version   1.7.0.0 May 5, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class Rockettheme_ContentSlider_Model_Contentslidertype
{

    public function toOptionArray()
    {
        return array(
			array('value'=>'products', 'label'=>Mage::helper('contentslider')->__('Show Catalog Products')),
            array('value'=>'custom', 'label'=>Mage::helper('contentslider')->__('Show Custom Content')),
        );
    }

}
