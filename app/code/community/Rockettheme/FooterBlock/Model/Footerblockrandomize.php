<?php
/**
 * @version   1.6.2.0 March 14, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class Rockettheme_FooterBlock_Model_Footerblockrandomize
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'rand()', 'label'=>Mage::helper('footerblock')->__('ON')),
            array('value'=>'', 'label'=>Mage::helper('footerblock')->__('OFF')),
        );
    }

}
