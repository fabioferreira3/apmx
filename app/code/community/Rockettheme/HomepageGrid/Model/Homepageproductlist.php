<?php
/**
 * @version   1.7.0.0 May 5, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class Rockettheme_HomepageGrid_Model_Homepageproductlist
{
    protected $_homepageproducts;
    
    public function toOptionArray($isMultiselect)
    {
        if (!$this->_homepageproducts) {
			$visibility = array(
			                      Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
			                      Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
			                  );
            $collection = Mage::getResourceModel('catalog/product_collection')
				->addAttributeToSelect('name')
				->addAttributeToFilter('visibility', $visibility)
				->addAttributeToFilter('status', 1)
				->setOrder('created_at', 'desc')
				->setPageSize(200)
				->load();
        }
       $homepageproducts = array();
        foreach ($collection as $product) {
            $homepageproducts[] = array(
               'label' => $product->getName(),
               'value' => $product->getData('entity_id')
            );
        }

        return $homepageproducts;
    }
 }




