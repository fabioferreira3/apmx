<?php
class Ophirah_Qquoteadv_Block_Qquoteadv_Email_Item extends Mage_Checkout_Block_Cart_Abstract {
    public $autoproposal = 0;
  
    /**
     * Get Product information from qquote_request_item table
     * @return object
     */
    public function getRequestedProductData($id, $quoteId) {
        $prices = array();
        $aQty   = array();
        
        $collection = Mage::getModel('qquoteadv/requestitem')->getCollection()
                        ->addFieldToFilter('quote_id', $quoteId)
                        ->addFieldToFilter('quoteadv_product_id', $id)
        ;
        $collection->getSelect()->order('request_qty asc');
        
        
        $n = count($collection);
        if ($n > 0) {
            foreach ($collection as $requested_item) {
                $aQty[]     = $requested_item->getRequestQty();
                $prices[]   = $requested_item->getOwnerBasePrice();
            }
        }
                
        return $return = array(
            'ownerPrices'=>$prices,
            'aQty'=>$aQty
        );
    }
    //#set value from template
    public function setAutoproposal($val){
      $this->autoproposal = $val;
      return $this;
    }
    
    public function isSetAutoProposal(){
      return $this->autoproposal;
    }    
}
