<?php
/**
 * @version   1.7.0.1 May 8, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class Rockettheme_MageMenus_Model_Menucontent
{

   protected $_options;

    public function toOptionArray()
    {
	$modules = Mage::getConfig()->getNode('modules')->children();
	$modulesArray = (array)$modules;
	if (!$this->_options) {
		if(isset($modulesArray['AW_Blog'])) {
			$array1 = Mage::getResourceModel('cms/page_collection')->addFieldToFilter('is_active', 1)->load()->toOptionIdArray();
			$array2 = array('blog'=>'AW Blog');
			$array3 = array_merge($array1, $array2);
			return array('disabled'=>'Disabled')+$array3;
	    	} else {
			$array1 = Mage::getResourceModel('cms/page_collection')->addFieldToFilter('is_active', 1)->load()->toOptionIdArray();
			return array('disabled'=>'Disabled')+$array1;
	    	}
	}
    }
 }