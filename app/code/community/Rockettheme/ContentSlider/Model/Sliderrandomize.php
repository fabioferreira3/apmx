<?php
/**
 * @version   1.7.0.0 May 5, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class Rockettheme_ContentSlider_Model_Sliderrandomize
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'rand()', 'label'=>Mage::helper('contentslider')->__('ON')),
            array('value'=>'', 'label'=>Mage::helper('contentslider')->__('OFF')),
        );
    }

}
