<?php
/**
 * @version   1.6.2.1 March 14, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class Rockettheme_ProductView_Model_Attributelist
{
    public function toOptionArray()
    {
        
        $collection = Mage::getResourceModel('eav/entity_attribute_collection')
			->setEntityTypeFilter( Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId() )
            ->load();

        $array3 = array();

        foreach ($collection as $attribute) {
			if($attribute->getIsVisible() && $attribute->getFrontendInput() == 'text' OR $attribute->getIsVisible() && $attribute->getFrontendInput() == 'textarea'):
            $array4[] = array(
               'label' => $attribute->getFrontendLabel(),
               'value' => 'ATT_'.$attribute->getAttributeCode()
            );
			endif;
			
			$array1 = array('disabled'=>'Disabled');
			$array2 = array('attributes'=>'-------- Attributes -------');			
			$array3 = array('additional'=>'Additional Information');
			$array5 = array('cmsblocks'=>'-------- CMS Blocks -------');
			$array6 = Mage::getModel('cms/block')->getCollection()->load()->toOptionArray();
			$array7 = array_merge($array1, $array2, $array3, $array4, $array5, $array6);
		
        }

        return $array7;

    }
 }




