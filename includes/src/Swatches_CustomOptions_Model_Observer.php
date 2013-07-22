<?php
class Swatches_CustomOptions_Model_Observer
{
    public function addImagetoResult(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        if ($collection instanceof Mage_Catalog_Model_Resource_Product_Option_Value_Collection) {
            if ($collection->count()) {
                $imageTable = $collection->getTable('swatches_customoptions/images');
                $select = $collection->getConnection()->select()
                    ->from(array('image' => $imageTable))
                    ->where('image.option_type_id IN (?)', $collection->getAllIds())
                    ->where('image.store_id = ?', Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);
                $query = $collection->getConnection()->query($select);
                while ($row = $query->fetch()) {
                    $collection->getItemById($row['option_type_id'])
                        ->setImage($row['image']);
                }               
            }
        }
        return $this;
    }
}
