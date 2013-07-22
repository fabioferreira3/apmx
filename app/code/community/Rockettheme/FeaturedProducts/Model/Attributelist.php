<?php
/**
 * @version   1.6.2.0 March 14, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class Rockettheme_FeaturedProducts_Model_Attributelist
{
    public function toOptionArray()
    {
        

        $collection = Mage::getResourceModel('eav/entity_attribute_collection');

        $collection->setEntityTypeFilter( Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId() )

            ->load();

        $options = array();

 
        foreach ($collection as $category) {
            $options[] = array(
               'label' => $category->getName(),
               'value' => $category->getName()
            );
        }

        return $options;

    }
 }




