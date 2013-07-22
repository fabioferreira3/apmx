<?php

class Ophirah_Qquoteadv_Model_Qqadvproduct extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('qquoteadv/qqadvproduct');
    }

	/**
	 * Delete product from quote
	 * @param integer $id id
	 */
	public function deleteQuote($id)
	{
		$this->setId($id)
		      ->delete()
		      ;
		return $this;
	}

	/**
	 * Get product for the particular quote
	 * @param integer $quoteId
	 * @return object product information
	 */
	public function getQuoteProduct($quoteId)
	{
		return $this->getCollection()
		            ->addFieldToFilter('quote_id',$quoteId)
		            ;
	}

	/**
	 * Add product for the particular quote to qquote_product table
	 * @param array $params product information to be added
	 *
	 */
	public function addProduct($params)
	{
		$this->setData($params)
		      ->save()
		      ;

		return $this;
	}

	/**
	 * Update product if the product is already added to the table by the customer for the particular session
	 * @param integer $id row id to be updated
	 * @param array $params array of field(s) to be updated
	 */
	public function updateProduct($id,$params)
	{
		$this->load($id)
		      ->addData($params)
		      ->setId($id)
		      ->save()
		      ;

		return $this;
	}


	public function addNotes($params){
	    foreach($params as $key=>$arr){
	        $item =  Mage::getModel('qquoteadv/qqadvproduct')->load($arr['id']);
            try{
                $item->setClientRequest($arr['client_request'])->save();   
            }catch(Exception $e){
                
            }
	   }
	    return $this;
	}

	public function getIdsByQuoteId($quoteId){
	   $ids = array();
	   $collection =  Mage::getModel('qquoteadv/qqadvproduct')->getCollection()
	                               ->addFieldToFilter('quote_id',$quoteId);

	   foreach($collection as $item){
	       $ids[] = $item->getId();
	   }

	    return $ids;
	}

	public function checkIncrementQty($id, $qty){
	
	  $product = Mage::getModel('catalog/product')->load($id);
	  $stockItem = $product->getStockItem();
	  $qtyIncrements =  $stockItem->getQtyIncrements();
	 	  
		$result = new Varien_Object();
		if ($qtyIncrements && (Mage::helper('core')->getExactDivision($qty, $qtyIncrements) != 0)) {
				$result->setHasError(true)
					  ->setProductUrl($product->getProductUrl())
						->setQuoteMessage(
								 Mage::helper('qquoteadv')->__('Some of the products cannot be quotation in the requested quantity.')
						)
						->setErrorCode('qty_increments')
						->setQuoteMessageIndex('qty');
				if ($this->getIsChildItem()) {
						$result->setMessage(
								Mage::helper('qquoteadv')->__('%s is available for quotation in increments of %s only.',$this->getProductName(), $qtyIncrements * 1)
						);
				} else {
						$result->setMessage(
								Mage::helper('qquoteadv')->__('This product is available for quotation in increments of %s only.', $qtyIncrements * 1)
						);
				}
		}
		return	 $result;
	}

}