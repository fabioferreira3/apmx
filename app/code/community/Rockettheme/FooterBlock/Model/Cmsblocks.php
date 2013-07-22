<?php
/**
 * @version   1.6.2.0 March 14, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class Rockettheme_FooterBlock_Model_Cmsblocks
{

   protected $_options;

    public function toOptionArray()
    {
	if (!$this->_options) {
			$array = Mage::getModel('cms/block')->getCollection()->load()->toOptionArray();
			return array('disabled'=>'Disabled')+$array;
		}
    }
 }