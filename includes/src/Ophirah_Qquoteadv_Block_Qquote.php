<?php
class Ophirah_Qquoteadv_Block_Qquote extends  Mage_Checkout_Block_Cart_Abstract //Mage_Core_Block_Template
{
	public function _prepareLayout() {
		return parent::_prepareLayout();
    }

	/**
	* Get customer session data
	* @return session data
	*/
	public function getCustomerSession() {
		return Mage::getSingleton('customer/session');
	}

	/**
	* Get product Information
	* @param integer $productId
	* @return session data
	*/
	public function getProduct($productId) {
		return Mage::getModel('catalog/product')->load($productId);
	}

	/**
	* Delete items with attribute allowed_to_quotemode=false
	* @param object Ophirah_Qquoteadv_Model_Mysql4_qqadvproduct_Collection
	*/
	protected function _notAllowedDelete(Ophirah_Qquoteadv_Model_Mysql4_qqadvproduct_Collection $collection){
        if (count($collection)) {
            foreach ($collection as $item) {
                $productId = $item->getProductId();

                $_product = Mage::getModel('catalog/product')->load($productId);				
               
                if (!$_product->getData('allowed_to_quotemode')) { 
                  $collection->walk('delete');
                }
            }
        }
	}

	/**
	 * Get Product information from qquote_product table
	 * @return quote object
	 */
	public function getQuote()
    { 
    	$quoteId = $this->getCustomerSession()->getQuoteadvId();
		$collection = Mage::getModel('qquoteadv/qqadvproduct')->getCollection()
						->addFieldToFilter('quote_id',$quoteId)
						//->load(true)
						;

		$this->_notAllowedDelete($collection);
		return $collection;
    }

	/**
	 * Get attribute options array
	 * @param object $product
	 * @param string $attribute
	 * @return array
	 */
	public function getOption($product, $attribute)
	{
		$superAttribute = array();
        
		if($product->getTypeId() == 'simple') { 
			$superAttribute = Mage::helper('qquoteadv')->getSimpleOptions($product, @unserialize($attribute));
		}
		return $superAttribute;
	}

    public function getContinueShoppingUrl()
    {
        $url = $this->getData('continue_shopping_url');
        if (is_null($url)) {
            $url = Mage::getSingleton('checkout/session')->getContinueShoppingUrl(true);
            if (!$url) {
                $url = Mage::getUrl();
            }
            $this->setData('continue_shopping_url', $url);
        }
        return $url;
    }
	
	public function getNotSalableProductNames($collection) {
	  $productNames = array(); 
	  foreach($collection as $_item) {
		$product = $this->getProduct($_item->getProductId());
		try{
		  if(!$product->isSaleable()) {
			$productNames[] = $product->getName();
		  }
		}catch(Exception $e){ Mage::log($e->getMessage()); }
	  }
	  return $productNames;
	}
    
    public function getOptionList($product, $item_attributes){
       return $this->getOption($product, $item_attributes);
    }
    
    public function getFormatedOptionValue($optionValue){
        /* @var $helper Mage_Catalog_Helper_Product_Configuration */
        $helper = Mage::helper('catalog/product_configuration');
        $params = array(
            'max_length' => 55,
            'cut_replacer' => ' <a href="#" class="dots" onclick="return false">...</a>'
        );
        return $helper->getFormattedOptionValue($optionValue, $params);
    }
}