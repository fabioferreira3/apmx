<?php
require_once 'Mage/Checkout/controllers/OnepageController.php';
class MW_Onestepcheckout_IndexController extends Mage_Checkout_OnepageController
//class MW_Onestepcheckout_IndexController extends Mage_Core_Controller_Front_Action
{	protected $notshiptype=0;
	public function getCheckout(){
		return Mage::getSingleton('checkout/session');
	}
	public function getQuote(){
		return Mage::getSingleton('checkout/session')->getQuote();
	}
	public function getOnepage()
	{
	 return Mage::getSingleton('checkout/type_onepage');
	}
	
    protected function _getQuote()
	{
	 return Mage::getSingleton('checkout/cart')->getQuote();
	}	
    public function indexAction()
    {
    	
    	
    	//$request = Mage::app()->getFrontController()->getRequest()->getRequestUri();
    	//var_dump($request);die();
////////////////////geoip///////		
		$this->getGeoip();
				
///////////////if not allow guest checkout and guest is not login, redirect to login page/////////////
//        if(!Mage::getStoreConfig('onestepcheckout/config/allowguestcheckout') or !Mage::getStoreConfig('checkout/options/guest_checkout')){
//    		if(!Mage::getSingleton('customer/session')->isLoggedIn()){
//				$this->_redirect('customer/account/login');
//				return;
//    		}
//		}
		$changestyle = 0;
		if($this->_initAction()){   
		// check operation system and change style to default if os is mobile, androi, iphone     
						
			Mage::getSingleton('core/session')->unsOs();
    		if(!Mage::getSingleton('core/session')->getOs())
    		{
	    		Mage::getSingleton('core/session')->setOs('notchange');
	    		$disable_os = Mage::getStoreConfig('onestepcheckout/config/disable_os');
	    		$arrdisable_os = explode(',', $disable_os);
//		    	$os_array = array( //'/windows nt 6.1/i'      =>  'Windows 7',
//		    						//'/windows xp/i'         =>  'Windows XP',
//		    						'/iphone/i'             =>  'iPhone',
//		                            '/ipod/i'               =>  'iPod',
//		                            '/ipad/i'               =>  'iPad',
//		                            '/android/i'            =>  'Android',
//		                            '/blackberry/i'         =>  'BlackBerry',
//		                            '/webos/i'              =>  'Mobile');
		    	
		    	$user_agent=$_SERVER['HTTP_USER_AGENT'];
		    	$redirect_os = false;			    		
		    	if(count($arrdisable_os)>0)
		    	{
			    	foreach ($arrdisable_os as $regex) //	foreach ($os_array as $regex => $value) 
			    		{ 			    			
			    			if(strlen(trim($regex))>3)
			    			{
				    			$regex_prg = '/'.trim($regex).'/i';
				    			
						        if (preg_match($regex_prg, $user_agent)) 
						        {
						            //$os_platform    =   $value;	
						            Mage::getSingleton('core/session')->setOs('change');			          
						            $redirect_os = true;
						        }
			    			}
			    		}   
			    		
			    	if($redirect_os)
			    	{
			    		$changestyle = 1;		    		
			    	}
			    	else 
			    	{
			    		if(Mage::getStoreConfig('onestepcheckout/config/enabled')){
							Mage::getSingleton('core/session')->setOs('notchange');
						//performance remove block 
							$changestyle = 0;	
						}
						else 
						{
							Mage::getSingleton('core/session')->setOs('change');
							$changestyle = 1;
						}
			    	}
		    	}
			}
			
			if($changestyle == 1)
			{
						$blocks=$this->getLayout()->getBlock('content')->unsetChild('onestepcheckout.daskboard');
						$skin=$this->getLayout()->getBlock('head')->unsetChild('onestepcheckout.head');
						
						if(Mage::helper('onestepcheckout/data')->getPlatform() == 3)
						$template=$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
						else 			
						$template=$this->getLayout()->getBlock('root')->setTemplate('page/2columns-right.phtml');			
			}
			else 
			{
						if($blocks=$this->getLayout()->getBlock('checkout.onepage')){	//neu ton tai block checkout.onepage
						$blocks=$this->getLayout()->getBlock('checkout.onepage')->unsetChildren();	//remove block checkout.onepage cua magento
						}
			}			
			$this->renderLayout();
		}
		else
			$this->_redirect('checkout/cart');

    }
	
    public function _initAction()
    {
        if (!Mage::helper('checkout')->canOnepageCheckout()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The onepage checkout is disabled.'));
            //$this->_redirect('checkout/cart');
            return false;
        }
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            //$this->_redirect('checkout/cart');
            return false;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            //$this->_redirect('checkout/cart');
            return false;
        }
        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure'=>true)));
        $this->getOnepage()->initCheckout();
		
		//luu thong tin bat ki` de? show duoc method shipping		

		$this->initInfoaddress();//khoi tao address tam thoi	
		$defaultpaymentmethod=$this->initpaymentmethod();  // ex: $defaultpaymentmethod='checkmo';
		if($defaultpaymentmethod){			
			try{
			Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->setPaymentMethod($defaultpaymentmethod);			
			$payment = Mage::getSingleton('checkout/session')->getQuote()->getPayment();
			$payment->importData(Array('method'=>$defaultpaymentmethod));
			}catch(Exception $e){				
			}
		}
		$defaultshippingmethod=$this->initshippingmethod();  //ex: $defaultshippingmethod='flatrate_flatrate';
		if($defaultshippingmethod){		
			Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->setShippingMethod($defaultshippingmethod);			
		}
		
		///////fix cho truong hop Promotions ->Shopping Cart Price Rule co' apply action = cart_fixed////
		// cho phep tinh lai discount
		$applyrule=$this->getQuote()->getAppliedRuleIds();		//
		$applyaction=Mage::getModel('salesrule/rule')->load($applyrule)->getSimpleAction();		
		if($applyaction!='cart_fixed'){
			Mage::getSingleton('checkout/session')->getQuote()->setTotalsCollectedFlag(false);
		}	

		///////////////end init address/////////////////
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('checkout/session');
		$this->_initLayoutMessages('catalog/session');
		Mage::getSingleton('catalog/session')->getData('messages')->clear();
		Mage::getSingleton('checkout/session')->getData('messages')->clear();	// clear het message thong bao vi trong template coupon co nhung' code xuat message thong bao'
        $this->getLayout()->getBlock('head')->setTitle($this->__('Checkout'));
        
		return true;
    }
	public function initshippingmethod(){
		$listmethod='';
		$guessCustomer = Mage::getSingleton('checkout/session')->getQuote();
		$addresses=$guessCustomer->getShippingAddress();  //ship method ko luu trong billing ma luu trong shipping
		$list_shipmethod=$addresses->getGroupedAllShippingRates();
		
		foreach ($list_shipmethod as $code => $_rates){
			$listmethod[]=$code;
		}
		
		if(!$guessCustomer->isVirtual()){
			if($listmethod==null){return;}
			if(sizeof($listmethod)==1){
			
				return $listmethod[0].'_'.$listmethod[0];
			}else{
				foreach($listmethod as $methodname){//echo $methodname.'=='.Mage::getStoreConfig("onestepcheckout/config/default_shippingmethod")."<br>";
					if(Mage::getStoreConfig("onestepcheckout/config/default_shippingmethod")==$methodname.'_'.$methodname){
								return $methodname.'_'.$methodname;						
					}
				}
			}
		}
		return;
	}
    public function _canUseMethod($method) //ham kiem tra payment method co thoa man cac dieu kien ko
    {
        if (!$method->canUseForCountry($this->getQuote()->getBillingAddress()->getCountry())) {
            return false;
        }

        if (!$method->canUseForCurrency(Mage::app()->getStore()->getBaseCurrencyCode())) {
            return false;
        }

        /**
         * Checking for min/max order total for assigned payment method
         */
        $total = Mage::getSingleton('checkout/session')->getQuote()->getBaseGrandTotal();
        $minTotal = $method->getConfigData('min_order_total');
        $maxTotal = $method->getConfigData('max_order_total');

        if((!empty($minTotal) && ($total < $minTotal)) || (!empty($maxTotal) && ($total > $maxTotal))) {
            return false;
        }
        return true;
    }
	public function initpaymentmethod(){
		$listmethod='';
		
		
		$guessCustomer = Mage::getSingleton('checkout/session')->getQuote();
		
		$store = $guessCustomer ? $guessCustomer->getStoreId() : null;
		$methods = Mage::helper('payment')->getStoreMethods($store, $guessCustomer);
		
        $billingCountry = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress()->getCountryId();
		
		foreach ($methods as $key => $method) {// echo get_class($methods);die;
			if ($this->_canUseMethod($method)) {			
					$listmethod[]=$method->getCode();						
			}
		}
		
		try{
			if($listmethod ==null or $listmethod==''){return;}
			if(sizeof($listmethod)==1){				
				return $listmethod[0];
			}else{
				foreach($listmethod as $methodname){
					if(Mage::getStoreConfig("onestepcheckout/config/default_paymentmethod")==$methodname){//echo $methodname.'=='.Mage::getStoreConfig("onestepcheckout/config/default_paymentmethod")."<br>";
						return $methodname;
					}
				}
			}
			return;
        }catch (Exception $e) {
			echo $e->getMessage();die;
		}			
	}
	
	public function initInfoaddress(){
			$coutryid='';$postcode='';$region='';$regionid='';$city='';$customerAddressId='';
			$countrygeo=Mage::registry('Countrycode');
				if(Mage::getStoreConfig('onestepcheckout/config/enable_geoip') && !empty($countrygeo)){
					$coutryid=Mage::registry('Countrycode');
					$postcode=Mage::registry('Zipcode');
					$region=Mage::registry('Regionname');
					if(Mage::getModel('customer/address_abstract')->getRegionModel(Mage::registry('Regioncode')))	//kiem tra regioncode co ton tai hay ko
						$regionid=Mage::registry('Regioncode');
					$city=Mage::registry('City');
				}
				elseif (Mage::getStoreConfig('onestepcheckout/config/default_country')) {
					 $coutryid = Mage::getStoreConfig('onestepcheckout/config/default_country');	// xuat ra voi' default country duoc cau hinh trong onestepcheckout
				}
				else {
					$coutryid = Mage::getStoreConfig('general/country/default');	// xuat ra voi' default country duoc cau hinh trong GENERAL DEFAULT CUA MAGENTO
				}
		
				$postData=array(
					'address_id'=>'',
					'firstname'=>'',
					'lastname'=>'',
					'company'=>'',
					'email'=>'',
					'street'=>array('',''),
					'city'=>$city,
					'region_id'=>$regionid,
					'region'=>$region,	
					'postcode'=>$postcode,
					'country_id'=>$coutryid,
					'telephone'=>'',
					'fax'=>'',
					'save_in_address_book'=>'0'
				);			
			if(Mage::getSingleton('customer/session')->isLoggedIn()){
				$customerAddressId =Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling();    
				
			}				
        	
        	if(($postData['country_id']!='')  OR $customerAddressId){
	        		$postData = $this->filterdata($postData);	        		
					//echo"<pre>";var_dump($postData);die();
					if(version_compare(Mage::getVersion(),'1.4.0.1','>='))
						$data = $this->_filterPostData($postData);
					else
						$data=$postData;
	           	 	
	        	   	if(isset($data['email'])) {
	                	$data['email'] = trim($data['email']);
	            	}
	
				$this->saveBilling($data, $customerAddressId);	            
				$this->saveShipping($data, $customerAddressId);				
        		}
        		else{
 					 $this->_getQuote()->getShippingAddress()
					 ->setCountryId('')
					 ->setPostcode('')	
					 ->setCollectShippingRates(true);
					 $this->_getQuote()->save();
					 $this->loadLayout()->renderLayout();  
					 return;        			
        		}	
	}
    public function saveShipping($data, $customerAddressId)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
        }
        $address = $this->getQuote()->getShippingAddress();

        if (!empty($customerAddressId)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
                    return array('error' => 1,
                        'message' => $this->_helper->__('Customer Address is not valid.')
                    );
                }
                $address->importCustomerAddress($customerAddress);
            }
        } else {
            unset($data['address_id']);
            $address->addData($data);
        }
        $address->implodeStreetAddress();
        $address->setCollectShippingRates(true);

       
    }
    public function saveBilling($data, $customerAddressId)
    { 
		if (empty($data)) {
					return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
		}		
        $address = $this->getQuote()->getBillingAddress();
        if (!empty($customerAddressId)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);           
            if ($customerAddress->getId()) {
                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
                    return array('error' => 1,
                        'message' => $this->_helper->__('Customer Address is not valid.')
                    );
                }
                $address->importCustomerAddress($customerAddress);
            }
        } 
        else {
             unset($data['address_id']);
             $address->addData($data);
        }

         $address->implodeStreetAddress();

        if (!$this->getQuote()->isVirtual()) {           
                     $shipping = $this->getQuote()->getShippingAddress();
                     $shipping->setSameAsBilling(0);
                   
        }

    }
    protected function _processValidateCustomer(Mage_Sales_Model_Quote_Address $address)
    {
        // set customer date of birth for further usage
        $dob = '';
        if ($address->getDob()) {
            $dob = Mage::app()->getLocale()->date($address->getDob(), null, null, false)->toString('yyyy-MM-dd');
            $this->getQuote()->setCustomerDob($dob);
        }

        // set customer tax/vat number for further usage
        if ($address->getTaxvat()) {
            $this->getQuote()->setCustomerTaxvat($address->getTaxvat());
        }

        // set customer gender for further usage
        if ($address->getGender()) {
            $this->getQuote()->setCustomerGender($address->getGender());
        }

        // invoke customer model, if it is registering
        if ($this->getQuote()->getCheckoutMethod()=='register') {
            // set customer password hash for further usage
            $customer = Mage::getModel('customer/customer');
            $this->getQuote()->setPasswordHash($customer->encryptPassword($address->getCustomerPassword()));

            // validate customer
            foreach (array(
                'firstname'    => 'firstname',
                'lastname'     => 'lastname',
                'email'        => 'email',
                'password'     => 'customer_password',
                'confirmation' => 'confirm_password',
                'taxvat'       => 'taxvat',
                'gender'       => 'gender',
            ) as $key => $dataKey) {
                $customer->setData($key, $address->getData($dataKey));
            }
            if ($dob) {
                $customer->setDob($dob);
            }
            $validationResult = $customer->validate();
            if (true !== $validationResult && is_array($validationResult)) {
                return array(
                    'error'   => -1,
                    'message' => implode(', ', $validationResult)
                );
            }
        } elseif(self::METHOD_GUEST == $this->getQuote()->getCheckoutMethod()) {
            $email = $address->getData('email');
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                return array(
                    'error'   => -1,
                    'message' => $this->_helper->__('Invalid email address "%s"', $email)
                );
            }
        }

        return true;
    }
	public function updateshippingmethodAction()
	{
		$data = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->getOnepage()->saveShippingMethod($data);            
            if(!$result) {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method',
                        array('request'=>$this->getRequest(),
                            'quote'=>$this->getOnepage()->getQuote()));
                $this->getOnepage()->getQuote()->collectTotals();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));         
            }
            $this->getOnepage()->getQuote()->collectTotals()->save();
		$this->loadLayout()->renderLayout();
	}
	public function updatepaymentmethodAction()
	{
		$data=$this->getRequest()->getPost('payment');			
		try{
			$this->getOnepage()->savePayment($data);
            }
	        catch (Exception $e) {             				
							$this->_getQuote()->save();             				
			}
		$this->loadLayout()->renderLayout();		
	}
	public function updateemailmsgAction(){
		$email = (string) $this->getRequest()->getParam('email');
		$websiteid=Mage::app()->getWebsite()->getId();
		$store=Mage::app()->getStore();
		$customer=Mage::getSingleton('customer/customer');
		$customer->website_id=$websiteid;
		$customer->setStore($store);
		$customer->loadByEmail($email);
		if($customer->getId()){			
			echo "0";
		}
		else {
			echo "1";

		}
		return;		
	}
	public function removeproductAction(){
			$id = (int) $this->getRequest()->getParam('id');
			$hasgiftbox=$this->getRequest()->getParam('hasgiftbox');
			if ($id) {
				try {
					Mage::getSingleton('checkout/cart')	->removeItem($id)
														->save();
														
				} catch (Exception $e) {					
					$success=0;
					echo '{"r":"'.$success.'","error":"'.Mage::helper('onestepcheckout')->__('Cannot remove the item.').'","view":' .json_encode($this->renderReview()).'}';die;
					return ;					
				}
			}
		
		$success=1; //gan' bien' success la co' error hay ko error , lay error tu message		
		if (!$this->_getQuote()->getItemsCount()) {	
			echo '{"r":"'.$success.'","view":"<script type=\"text/javascript\">window.location=\"'.Mage::getUrl('checkout/onepage').'\"</script>"}';die;
			return;//return giup thoat ra va ko thuc hien cac lenh con lai, neu thuc hien tiep se gay loi, vi ko con item nao trong gio hang			
		}
		else{
			if($hasgiftbox){ //neu co gift box thi phai tra ve du lieu update cho gift box
				echo '{"r":"'.$success.'","view":' .json_encode($this->renderReview()).',"giftbox":' .json_encode($this->renderGiftbox()) .'}';die;
				return ;
			}
			else{
				echo '{"r":"'.$success.'","view":' .json_encode($this->renderReview()).'}';die;
				return ;				
			}
		}
	}
	public function updateqtyAction(){
        try {
            $cartData = $this->getRequest()->getParam('cart');			
            if (is_array($cartData)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter($data['qty']);
                    }
                }
                $cart = Mage::getSingleton('checkout/cart');
                if (! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }
                $cart->updateItems($cartData)
                    ->save();
            }
            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
        }
        catch (Mage_Core_Exception $e) {           
			$success=0;
			echo '{"r":"'.$success.'","error":"'.$e->getMessage().'","view":' .json_encode($this->renderReview()).'}';die;
			return ;			
        }
        catch (Exception $e) {           
			$success=0;
			echo '{"r":"'.$success.'","error":"'.Mage::helper('onestepcheckout')->__('Cannot update shopping cart.').'","view":' .json_encode($this->renderReview()).'}';die;
			return ;			
        }	
		$success=1; //gan' bien' success la co' error hay ko error , lay error tu message		
		if (!$this->_getQuote()->getItemsCount()) {	
			echo '{"r":"'.$success.'","view":"<script type=\"text/javascript\">window.location=\"'.Mage::getUrl('checkout/onepage').'\"</script>"}';die;
			return;//return giup thoat ra va ko thuc hien cac lenh con lai, neu thuc hien tiep se gay loi			
		}
		else{
			echo '{"r":"'.$success.'","view":' .json_encode($this->renderReview()).'}';die;
			return ;			
		}		
	}
	public function updatecouponAction(){
		$this->_initLayoutMessages('checkout/session');
		if (!$this->_getQuote()->getItemsCount()) {
            //$this->_goBack();
			echo '{"r":"0","coupon":'.json_encode($this->renderCoupon()).',"view":' . json_encode($this->renderReview()).'}';die;
            return;
        }
		$couponCode = (string) $this->getRequest()->getParam('coupon_code');
		if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }
		$oldCouponCode = $this->_getQuote()->getCouponCode();//get couponcode cu~ da duoc apply truoc' do'
        if (!strlen($couponCode) && !strlen($oldCouponCode)) {		//2 nay deu rong thi` price ko do
         //   $this->_goBack();
			echo '{"r":"0","coupon":'.json_encode($this->renderCoupon()).',"view":' . json_encode($this->renderReview()).'}';die;
            return;
        }
        try {
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
		        ->collectTotals()
                ->save();
			//$this->sendHtmlto();
				if ($couponCode) {
					if ($couponCode == $this->_getQuote()->getCouponCode()) {
						Mage::getSingleton('checkout/session')->addSuccess(
							$this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode))
						);
					}
					else {
						Mage::getSingleton('checkout/session')->addError(
							$this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
						);
					}
				} else {
					Mage::getSingleton('checkout/session')->addSuccess($this->__('Coupon code was canceled.'));
				}				
		}
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('checkout/session')->addError($e->getMessage());
		}
        catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addError($this->__('Cannot apply the coupon code.'));
        }
		$this->_initLayoutMessages('checkout/session');
		$success=1; //gan' bien' success la co' error hay ko error , lay error tu message		
		echo '{"r":"'.$success.'","coupon":'.json_encode($this->renderCoupon()).',"view":' .json_encode($this->renderReview()).'}';die;
		return ;
	}
	public function renderReview(){
		$layout=$this->getLayout();
		$update = $layout->getUpdate();
		$update->load('checkout_onepage_index');
		$layout->generateXml();
		$layout->generateBlocks();
		$output=$layout->getBlock('info')->toHtml();
		return $output;		
	}
	public function renderCoupon(){
		$layout=$this->getLayout();
		$update = $layout->getUpdate();
		$update->load('checkout_onepage_index');
		$layout->generateXml();
		$layout->generateBlocks();
		
		$output = $layout->getBlock('checkout.onepage.coupon')->toHtml();		//lay rieng block checkout.onepage.coupon trong layout checkout_cart_index
		return $output;
		//$this->getResponse()->setBody($output);			
	}
	public function renderGiftbox(){
		$layout=$this->getLayout();
		$update = $layout->getUpdate();
		$update->load('checkout_onepage_index');
		$layout->generateXml();
		$layout->generateBlocks();
		
		$output = $layout->getBlock('onestepcheckout.onepage.shipping_method.additional')->toHtml();		//lay rieng block checkout.cart.coupon trong layout checkout_cart_index
		return $output;
		//$this->getResponse()->setBody($output);			
	}
	public function updatepaymenttypeAction(){
		if($this->notshiptype==1){
				$this->loadLayout()->renderLayout();
		}
		else{
			$this->updateshippingtypeAction();
		}
	}
	public function getvatAction(){
		 $countrycode = $this->getRequest()->getParam('countrycode');
		 $vat = $this->getRequest()->getParam('vatnumber');
	 	 $result = @file_get_contents('http://vatid.eu/check/'.$countrycode.'/'.$vat);
	 	 if($result)
	 	 {
	 	 	echo $result;
	 	 }
	 	 else 
	 	 {
	 	 	echo '<?xml version="1.0" encoding="UTF-8"?>
				<response>
				  <country-code>DK</country-code>
				  <vat-number>000000</vat-number>
				  <valid>false</valid>
				  <name>---</name>
				  <address>---</address>				  
				</response>';
	 	 }
	}
public function updateshippingtypeAction()	    
    {	
    	$this->notshiptype=1;		
        if ($this->getRequest()->isPost()) {
        	$isbilling="billing";
        	if($this->getRequest()->getPost('ship_to_same_address')=="1"){
        		$isbilling="billing";
        	}
        	else{
        		$isbilling="shipping";
        	}
        	$postData=$this->getRequest()->getPost($isbilling, array());
        	$customerAddressId = $this->getRequest()->getPost($isbilling.'_address_id', false); 
			// phai dua customerAddressId ve trong thi moi cap nhat lai shipping method duoc
			// khi thuc hien save vao order thi do place order xu ly
       		if ($this->getRequest()->getPost($isbilling.'_address_id') != "")
			{				
				$customerAddressId  = "";			
			}  	
        	
        		if(($postData['country_id']!='')  OR $customerAddressId){
        			
	        		$postData = $this->filterdata($postData);
	        		$postData['use_for_shipping']='1';	        		
					//echo"<pre>";var_dump($postData);die();
					if(version_compare(Mage::getVersion(),'1.4.0.1','>='))
						$data = $this->_filterPostData($postData);
					else
						$data=$postData;
//	           	 	if($data['postcode']=='.')
//	           	 	$data['postcode']='';
	        	   	if(isset($data['email'])) {
	                	$data['email'] = trim($data['email']);
	            		}
	            	if($isbilling="billing"){
	            	$result = $this->getOnepage()->saveBilling($data, $customerAddressId);}
	            	else
	            	$result = $this->getOnepage()->saveShipping($data, $customerAddressId);
	            	
        		}
        		else{
 					 $this->_getQuote()->getShippingAddress()
					 ->setCountryId('')
					 ->setPostcode('')	
					 ->setCollectShippingRates(true);
					 $this->_getQuote()->save();
					 $this->loadLayout()->renderLayout();  
					 return;        			
        		}
	    }
			
		$this->loadLayout()->renderLayout();
    }
	
	public function updatetimepickerAction()
	{	
		
		$dateisnow=$this->getRequest()->getPost('now');
		$starttime=$this->getRequest()->getPost('stime');
		$starray=explode(":",$starttime);		
		$count_stimetominutes=$starray[0]*60+$starray[1];
		$endtime=$this->getRequest()->getPost('etime');
		$etarray=explode(":",$endtime);	
		$count_etimetominutes=$etarray[0]*60+$etarray[1];
		
		$count_timenow=date("G", Mage::getModel("core/date")->timestamp(time()))*60+date("i", Mage::getModel("core/date")->timestamp(time()));
		if($dateisnow){
			if($count_timenow>=$count_stimetominutes){
				if($count_timenow<$count_etimetominutes){
					echo date("G", Mage::getModel("core/date")->timestamp(time())).":".date("i", Mage::getModel("core/date")->timestamp(time())) ;		//cho phep in tren timepicker time hien tai
				}
				else{
					echo "";		//time off
				}
			}
			else{
				echo $starttime;	//cho phep in start time
			}
		}
		else
			echo $starttime;//cho phep in start time vi` la ngay hom khac
	}

	public function _getSession(){
		Mage::getSingleton('customer/session');
	}
	
	public function updateloginAction()
	{
		// get user's email logined
        $email=$this->getRequest()->getPost('email');
        
        // get user's password logined
		$password=$this->getRequest()->getPost('password');
        if ($this->isCustomerLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }        
        if ($this->getRequest()->isPost()) {
            if (!empty($email) && !empty($password)) {
				try{
					Mage::getSingleton('customer/session')->login($email, $password);
				}catch(Mage_Core_Exception $e){					
				}catch(Exception $e){					
				}
			}
        }
        if (Mage::getSingleton('customer/session')->isLoggedIn()){
			echo "1";
		}
		else{
			echo "0";
		}
	}
	
	public function forgotpassAction(){
		$email=$this->getRequest()->getPost('email');
		$emailerror="0";
        if ($email) {
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $this->_getSession()->setForgottenEmail($email);
                $emailerror="0";
				echo $emailerror;
                return $emailerror;
            }
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

            if ($customer->getId()) {
                try {
                    $newPassword = $customer->generatePassword();
                    $customer->changePassword($newPassword, false);
                    $customer->sendPasswordReminderEmail();
					$emailerror="1";                    
					echo $emailerror;
                    return $emailerror;
                }
                catch (Exception $e){                  
                }
            }
            else {
				$emailerror="2";               
                Mage::getSingleton('customer/session')->setForgottenEmail($email);
				echo $emailerror;
				return;
            }
        } else {
			$emailerror="0";            
            echo $emailerror;
			return $emailerror;
        }

	}	

    	
	
	public function updateordermethodAction()
	{ 
		$vote = $this->getRequest()->getPost('vote');
		if($vote)
		{
			$this->voteAdd();
		}		
		if(!$this->isCustomerLoggedIn()){
			if(isset($_POST['register_new_account']))
			{
				$isguest=$this->getRequest()->getPost('register_new_account');				
				if($isguest=='1' or Mage::helper('onestepcheckout')->haveProductDownloadable()){ //neu checkbox regiter_new_accoutn dc check or co san pham downloadable thi tao new acc 
						$result_save_method = $this->getOnepage()->saveCheckoutMethod('register');
					}
				else{
						$result_save_method = $this->getOnepage()->saveCheckoutMethod('guest');	//save method
					}
			}
			else 
			{
				if(( !Mage::getStoreConfig('onestepcheckout/config/allowguestcheckout') || !Mage::getStoreConfig('checkout/options/guest_checkout') || Mage::helper('onestepcheckout')->haveProductDownloadable()))
				{
					$result_save_method = $this->getOnepage()->saveCheckoutMethod('register');
				}
				else{
						$result_save_method = $this->getOnepage()->saveCheckoutMethod('guest');	//save method
					}
			}
		}		
///////////////// Billing
        if ($this->getRequest()->isPost()) {
			//if(Mage::getStoreConfig('onestepcheckout/config/is_sort_add')) // allow sort fields
				$data_save_billing =$this->filterdata($this->getRequest()->getPost('billing', array()),false);	//khi chua login
			//else
			//	$data_save_billing = $this->getRequest()->getPost('billing', array());	
				
							
			// thuc hien update address neu chon save in address va billing_address_id != '', check trong ham save address 
			if($this->isCustomerLoggedIn())
			 {		
			 	// save billing when save in address book =1	 	
			 	$this->saveAddress('billing', $data_save_billing);				 			 		 
			 }
			 
			// xac dinh address qua addressid de xu ly
			$customerAddressId = $this->getRequest()->getPost('billing_address_id', false);			
			// Khi thay doi thong tin billing nhung khong save vao csdl, co anh huong den shipping nua, neu khong shipping to same address =1
			if ($this->getRequest()->getPost('billing_address_id') != "" && (!isset($data_save_billing['save_in_address_book']) || (isset($data_save_billing['save_in_address_book'])&& $data_save_billing['save_in_address_book'])==0) )
			{				
				$customerAddressId  = "";			
			}			
			if (isset($data_save_billing['email'])) {
				$data_save_billing['email'] = trim($data_save_billing['email']);
			}	
			if($this->getRequest()->getPost('subscribe_newsletter')=='1'){
				if($this->isCustomerLoggedIn()){
					$customer = Mage::getSingleton('customer/session')->getCustomer();
					$customer->setIsSubscribed(1);
				}else{
					$this->savesubscibe($data_save_billing['email']);
				}
			}			
			$result_save_billing = $this->getOnepage()->saveBilling($data_save_billing, $customerAddressId);
			
			// session deliverydate time, process afterSave in Observer.php, Order.php(email)
			$data_customercomment = $this->getrequest()->getpost('onestepcheckout_comments');			
			$Deliverystatus= $this->getrequest()->getpost('deliverydate');
			$Deliverydate = $this->getrequest()->getpost('onestepcheckout_date');
			
			$Deliverytime= $this->getrequest()->getpost('onestepcheckout_time');
			if(Mage::getStoreConfig("onestepcheckout/deliverydate/timerange"))
			$Deliverytime= $this->getrequest()->getpost('delivery-timerange');		
			
			$delivery_infor=array($data_customercomment,$Deliverystatus,$Deliverydate,$Deliverytime);	
			Mage::getSingleton('core/session')->setDeliveryInforOrder($delivery_infor);
			Mage::getSingleton('core/session')->setDeliveryInforEmail($delivery_infor);  
			
			// session account infomation, process afterSave in Observer.php	
			if(isset($data_save_billing['save_into_account']) && intval($data_save_billing['save_into_account'])==1)
			{		
				if($this->isCustomerLoggedIn())
				 {		
				 	//$editaccount=$data_save_billing['save_in_account'];	
				 	$dob="";	
				 	if(isset($data_save_billing['dob']))
				 	$dob = $data_save_billing['dob'];
				 	
				 	$gender="";	
				 	if(isset($data_save_billing['gender']))
				 	$gender = $data_save_billing['gender'];
				 	
				 	$taxvat="";	
				 	if(isset($data_save_billing['taxvat']))
				 	$taxvat = $data_save_billing['taxvat'];
	
				 	$suffix="";	
				 	if(isset($data_save_billing['suffix']))
				 	$suffix = $data_save_billing['suffix'];
				 	
				 	$prefix="";	
				 	if(isset($data_save_billing['prefix']))
				 	$prefix = $data_save_billing['prefix'];
				 	
				 	$middlename="";	
				 	if(isset($data_save_billing['middlename']))
				 	$middlename = $data_save_billing['middlename'];		       
					
					$middlename="";	
				 	if(isset($data_save_billing['middlename']))
				 	$middlename = $data_save_billing['middlename'];	
				 	
				 	$firstname="";
				 	if(isset($data_save_billing['firstname']))
				 	$firstname = $data_save_billing['firstname'];
				 	
				 	$lastname="";
				 	if(isset($data_save_billing['lastname']))
				 	$firstname = $data_save_billing['lastname'];
					
					$account_infor = array($dob,$gender,$taxvat,$suffix,$prefix,$middlename,$firstname,$lastname);		//,$editaccount		
					Mage::getSingleton('core/session')->setAccountInfor($account_infor);
				 }  
			}     
		}
		
/////////////// Shipping
		$isclick=$this->getRequest()->getPost('ship_to_same_address');
		$ship="billing";
		if(!$isclick=='1'){
				$ship="shipping";
		}		
		if ($this->getrequest()->ispost()) {
			//if(Mage::getStoreConfig('onestepcheckout/config/is_sort_add'))
				$data_save_shipping = $this->filterdata($this->getrequest()->getpost($ship, array()),false);
			//else
			//	$data_save_shipping = $this->getrequest()->getpost($ship, array());			 
				
		// thuc hien update address neu chon save in address 
			if($this->isCustomerLoggedIn() && !$isclick)
			 {			 	
			 	$this->saveAddress('shipping', $data_save_shipping);
			 }
			 
		    if($isclick=='1'){
				$data_save_shipping['same_as_billing']=1;
			}
			
			// thuc hien sua thong tin address neu nguoi dung thay doi thong tin			
			// gan lai customeraddressid va save vao shipping	
			$customeraddressid = $this->getrequest()->getpost($ship.'_address_id', false);
			// neu nguoi dung thay doi thong tin billing, shipping nhung ko save vao csdl			
			if ($isclick || ($this->getRequest()->getPost('shipping_address_id') != "" && (!isset($data_save_shipping['save_in_address_book']) || (isset($data_save_shipping['save_in_address_book']) && $data_save_shipping['save_in_address_book'] ==0 ) )))
			{
				$customeraddressid  = "";			
			}

			$result_save_shipping = $this->getonepage()->saveshipping($data_save_shipping, $customeraddressid);	
			//save shipping						
		}
	
/////////////// Shipping method		
		if ($this->getRequest()->isPost()) {
			$data_save_shipping_method = $this->getRequest()->getPost('shipping_method', '');	
			$result_save_shipping_method = $this->getOnepage()->saveShippingMethod($data_save_shipping_method);				//save shipping_method
			if(!$result_save_shipping_method) {		//cho phep save gift message
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request'=>$this->getRequest(), 'quote'=>$this->getOnepage()->getQuote()));
				$this->getOnepage()->getQuote()->collectTotals();
			}
			$this->getOnepage()->getQuote()->collectTotals();
		}		
	
/////////////// Payment method		
		$result_savepayment = array();
		$data_savepayment = $this->getRequest()->getPost('payment', array());
		try{
			$result_savepayment = $this->getOnepage()->savePayment($data_savepayment);				//save payment
		} 
		catch(Exception $e){
			$message = $e->getMessage();
			Mage::getSingleton('checkout/session')->addError($this->__($message));
			$this->_redirect('checkout/onepage/index');
			return;//return giup thoat ra va ko thuc hien cac lenh con lai, neu thuc hien tiep se gay loi
		}
		$redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
		if(isset($redirectUrl))
		{
			$this->_redirectUrl($redirectUrl);
			return;
		}
		
		 $result_order = array();
		 if ($data_order = $this->getRequest()->getPost('payment', false)) 
		 	 {
                 $this->getOnepage()->getQuote()->getPayment()->importData($data_order);
             }
             
/////////*Fix for Sagepay */		
		$paymentMethod = $this->getOnepage()->getQuote()->getPayment()->getMethod();
		
		//var_dump($paymentMethod);
		
		Mage::getSingleton('core/session')->unsErrorpayment(); // show error when paymenting on checkout page
		
	if($paymentMethod== 'sagepayserver')
		{
			
			$resultData = array ();
			try {
				Mage::helper('sagepaysuite')->validateQuote();	
				$result = Mage :: getModel('sagepaysuite/sagePayServer')->registerTransaction($this->getRequest()->getPost());				
				$resultData = $result->getData();		
				if ($result->getResponseStatus() == Ebizmarts_SagePaySuite_Model_Api_Payment :: RESPONSE_CODE_APPROVED) {
					$redirectUrl = $result->getNextUrl();
				}
				else 
				{				
					Mage::getSingleton('core/session')->setErrorpayment($resultData['response_status_detail']);
					$this->_redirect('checkout/onepage');
					return;
				}
			} catch (Exception $e) {
			$resultData['response_status'] = 'ERROR';
			$resultData['response_status_detail'] = $e->getMessage();
			Mage::getSingleton('core/session')->setErrorpayment($resultData['response_status_detail']);
			$this->_redirect('checkout/onepage');
					return;
			}
			if (isset($redirectUrl)) {
				$this->_redirectUrl($redirectUrl);
			}
											
		}
		else if($paymentMethod == 'sagepaydirectpro')
		{						
			$resultData = array ();
			try {		
					Mage::helper('sagepaysuite')->validateQuote();					
					$directModel = Mage :: getModel('sagepaysuite/sagePayDirectPro');		
					$result = $directModel->registerTransaction($this->getRequest()->getPost());
					$resultData = $result->getData();					
					$response_status = $result->getResponseStatus();
					
					if ($response_status == Ebizmarts_SagePaySuite_Model_Api_Payment :: RESPONSE_CODE_3DAUTH) {
					
						$this->_forward('_expireAjax','directPayment','sgps', $this->getRequest()->getParams());
						$this->_forward('threedPost','directPayment','sgps', $this->getRequest()->getParams());
						return;
					}
					 else
					{	
					 	//$this->_forward('_expireAjax','directPayment','sgps', $this->getRequest()->getParams());
 						try{	//kiem tra xem cac info nhu info payment co' phai? hop le hay la` gian lan, neu ko thi se bat loi~ =catch va` redirect
							$this->getOnepage()->saveOrder();
		         		   }
			       		 catch (Exception $e) {
		             				echo $e->getMessage();
		             				echo "<script type='text/javascript'>setTimeout('window.location=\"".Mage::getUrl('checkout/onepage')."\"',2000)</script>";//cho` 2 s de khach hang nhin thay loi va redirect
		             				return;//return giup thoat ra va ko thuc hien cac lenh con lai, neu thuc hien tiep se gay loi
		           				 }	
		           							
								//fix khi checkout song ma` cart van con san pham		
								$redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();			
								$result_order['success'] = true;
								$result_order['error']   = false;
								$cart = Mage::getModel('checkout/cart');	
								$cartItems = $cart->getItems(); 
								foreach ($cartItems as $item){
									$cart->removeItem($item->getId())->save();
								}
								 $this->getOnepage()->getQuote()->save();
								if(isset($redirectUrl))
								{
									$this->_redirectUrl($redirectUrl);
									return;
								}									
								$this->_redirect('checkout/onepage/success');
					 }
				} 
				catch (Exception $e) {					
					Sage_Log :: logException($e);
					$result_order['response_status'] = 'ERROR';
					$result_order['response_status_detail'] = $e->getMessage();	
					// redirect order error
					Mage::getSingleton('core/session')->setErrorpayment($result_order['response_status_detail']);
					$this->_redirect('checkout/onepage');
					return;
				}
		}
		elseif($paymentMethod == 'sagepayform')
		{		
			// store session delivery date, comment content				
			
				Mage::helper('sagepaysuite')->validateQuote();
				$this->_forward('_initCheckout', 'formPayment', 'sgps',$this->getRequest()->getPost());      
				$this->_forward('go', 'formPayment', 'sgps',$this->getRequest()->getPost());	
		        return;		
			
		}	
       else 
       {      
		       try{	//kiem tra xem cac info nhu info payment co' phai? hop le hay la` gian lan, neu ko thi se bat loi~ =catch va` redirect
					$this->getOnepage()->saveOrder();
		            }
			        catch (Exception $e) {
		             				echo $e->getMessage();
		             				echo "<script type='text/javascript'>setTimeout('window.location=\"".Mage::getUrl('checkout/onepage')."\"',2000)</script>";//cho` 2 s de khach hang nhin thay loi va redirect
		             				return;//return giup thoat ra va ko thuc hien cac lenh con lai, neu thuc hien tiep se gay loi
		            }			           					
					//fix khi checkout song ma` cart van con san pham		
					$redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();			
					$result_order['success'] = true;
					$result_order['error']   = false;
					$cart = Mage::getModel('checkout/cart');	
					$cartItems = $cart->getItems(); 
					foreach ($cartItems as $item){
						$cart->removeItem($item->getId())->save();
					}
					 $this->getOnepage()->getQuote()->save();
					if(isset($redirectUrl))
					{
						$this->_redirectUrl($redirectUrl);
						return;
					}
					$this->_redirect('checkout/onepage/success');
       }
		
	}
	
	protected function filterdata($data,$filter="true")	
	{
			
	$arrayname=array('address_id','firstname','lastname','company','email','city','region_id','region','postcode','country_id','telephone','fax','save_in_address_book');
	$filterdata=array();
	//neu filter true thi gan' n/a de save data khi ajax
	//neu filter false thi gan' null da save data khi place order 
	if($filter =='true'){		
		if(version_compare(Mage::getVersion(),'1.4.2.0','>=')){
			$filterdata=array(
				'prefix'=>'n/a',
				'address_id'=>'n/a',
				'firstname'=>'n/a',
				'lastname'=>'n/a',
				'company'=>'n/a',
				'email'=>'n/a@na.na',
				'street'=>array('n/a','n/a','n/a','n/a'),
				'city'=>'n/a',
				'region_id'=>'n/a',
				'region'=>'n/a'	,	
				'postcode'=>'.',
				'country_id'=>'n/a',
				'telephone'=>'n/a',
				'fax'=>'n/a',
				'month'=> null,
				'day'=>null,
				'year'=>null,
				'dob'=>'01/01/1900',
			    'gender'=>'n/a',
				'taxvat'=>'n/a',
				'suffix'=>'n/a',				
				'save_in_address_book'=>'0'
			);
		}
		else{
			
			$filterdata=array(
				'prefix'=>'n/a',
				'address_id'=>'n/a',
				'firstname'=>'n/a',
				'lastname'=>'n/a',
				'company'=>'n/a',
				'email'=>'n/a@na.na',
				'street'=>array(
							'street1'=>'street is null',
							'street2'=>'street is null',
							'street3'=>'street is null',
							'street4'=>'street is null'
						),
				'city'=>'n/a',
				'region_id'=>'n/a',
				'region'=>'n/a'	,	
				'postcode'=>'.',
				'country_id'=>'n/a',
				'telephone'=>'n/a',
				'fax'=>'n/a',
				'month'=> null,
				'day'=>null,
				'year'=>null,
				'dob'=>'01/01/1900',
				'gender'=>'n/a',
				'taxvat'=>'n/a',
				'suffix'=>'n/a',
				'save_in_address_book'=>'0'
			)	;	
		}
	}
	else{
		$filterdata=array(
			'prefix'=>'',
			'address_id'=>'',
			'firstname'=>'',
			'lastname'=>'',
			'company'=>'',
			'email'=>'',
			'street'=>array('','','',''),
			'city'=>'',
			'region_id'=>'',
			'region'=>''	,	
			'postcode'=>'.',
			'country_id'=>'',
			'telephone'=>'',
			'fax'=>'',
			'month'=> null,
			'day'=>null,
			'year'=>null,
			'dob'=>null,			
			'gender'=>'',
			'taxvat'=>'',
			'suffix'=>'',
			'save_in_address_book'=>'0'
		);	
	}
		foreach($data as $item=>$value){
			if(!is_array($value)){
					//if($value)	//lenh chuoc day gay loi save addressbook mac du value cua saveaddressbook =0
					if($value!='') // fix loi save address book voi saveaddressbook co value=0
						$filterdata[$item]=$value;
			}
			else{	//danh cho field street1 va street2
				$street=$value;
				$street_lines = Mage::getStoreConfig('onestepcheckout/addfield/street_lines');
				
				if($street[0])
				{	
					switch (intval($street_lines))
					{
						case 2:
							$filterdata[$item]=array($street[0],$street[1]);
							break;
						case 3:
							$filterdata[$item]=array($street[0],$street[1],$street[2]);
							break;
						case 4:
							$filterdata[$item]=array($street[0],$street[1],$street[2],$street[3]);
								break;
						default:
								$filterdata[$item]=array($street[0]);
								break;						
					}
				}
			}
		}		
		return $filterdata;
	}
	
    public function isCustomerLoggedIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }
    
    public function getGeoip(){					
					if(Mage::getStoreConfig('onestepcheckout/config/enable_geoip')){
					    	try {
							$key="718b4c6e9d4b09b15cbb35e846457723cd0fe7d842401c7203069b7252f7d289";
							//link==>  http://api.ipinfodb.com/v2/ip_query.php?key=718b4c6e9d4b09b15cbb35e846457723cd0fe7d842401c7203069b7252f7d289&ip=184.168.193.20&timezone=true
					 		$date= Mage::app()->getLocale()->date();   		
							$timezone_server=$date->get(Zend_Date::TIMEZONE_SECS)/3600;
							$timezone_client=$timezone_server;     		
								//$xml = simplexml_load_file('http://freegeoip.appspot.com/xml/'.$_SERVER['REMOTE_ADDR']);
								$xml = simplexml_load_file('http://api.ipinfodb.com/v2/ip_query.php?key='.$key."&ip=".$_SERVER['REMOTE_ADDR']."&timezone=true");
								// echo $xml->getName() . "<br />";
									// foreach($xml->children() as $child)
									  // {
									  // echo $child->getName() . ": " . $child . "</br>";
									  // }
									// echo "<br>";
								if(!$xml)
									return false;
								$info_ip_address=$xml->children();
								//echo $info_ip_address[2];die(); 
								if($info_ip_address[0] && !empty($info_ip_address[1])){
										$longitude=$info_ip_address[9];
										//$longitude=-25;
										$hourpart=(float)$longitude/15;
										$off=$hourpart-(int)$hourpart;
										
										$timezone_client=(int)$hourpart;
										
										if(abs($off) >0.5){
												$timezone_client=((int)$hourpart>0)?(int)$hourpart+1:(int)$hourpart-1;
											}
										Mage::register('Countrycode',$info_ip_address[1]);
										Mage::register('Countryname',$info_ip_address[2]);
										Mage::register('Regioncode',intval($info_ip_address[3]));
										Mage::register('Regionname',$info_ip_address[4]);
										Mage::register('City',$info_ip_address[5]);
										Mage::register('Zipcode',$info_ip_address[6]);
										Mage::register('Latitude',$info_ip_address[7]);
										Mage::register('Timezoneclient',$timezone_client);
										Mage::register('Timezoneserver',$timezone_server);
										//var_dump(Mage::helper('directory')->getRegionJson());die(); //return string container info region id
						        		$statesCollection = Mage::getModel('directory/region')->getResourceCollection()	//get info region with country 
						                   	 	->addCountryFilter($info_ip_address[2])
						                    	//->addCountryFilter('US')
						        				->load();
						               	//$items = array();
						               	//$i=0;
						               	//$regioncode="";
						       			foreach ($statesCollection as $state) {
						                    if (!$state->getRegionId()) {
						                        continue;
						                    }
						                	//$items[$state->getRegionId()] = $state->getCode();//$state->getName()
						       				if($state->getCode()==$info_ip_address[3]){
						                	//if($state->getCode()=='AZ'){
							       				//Mage::register('Regionid',$state->getRegionId());
							       				Mage::register('Regionid',$state->getRegionId());
							       				//$regioncode=$state->getRegionId();
							       				break;
						       				}
						       				//$i++;
						       			} 
						       			//echo $i."<pre>";var_dump($regioncode);die(); 
								}else {
								Mage::register('Countrycode','');
								}
								//echo $timezone;	
							} catch (Exception $e) {
							 	//echo "net die";die();
							}
					
					}//	end func geoip		
    }
	public function savesubscibe($mail)
    {
        if ($mail) {
			if(version_compare(Mage::getVersion(),'1.3.2.4','>')){
				//$session            = Mage::getSingleton('core/session');
				$session            = Mage::getSingleton('checkout/session');
				$customerSession    = Mage::getSingleton('customer/session');
				$email              = (string) $mail;

				try {
					if (!Zend_Validate::is($email, 'EmailAddress')) {
						Mage::throwException($this->__('Please enter a valid email address.'));
					}

					if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 && 
						!$customerSession->isLoggedIn()) {
						Mage::throwException($this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::getUrl('customer/account/create/')));
					}

					$ownerId = Mage::getModel('customer/customer')
							->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
							->loadByEmail($email)
							->getId();
					if ($ownerId !== null && $ownerId != $customerSession->getId()) {
						Mage::throwException($this->__('Sorry, but your can not subscribe email adress assigned to another user.'));
					}

					$status = Mage::getModel('newsletter/subscriber')->subscribe($email);
					if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
						$session->addSuccess($this->__('Confirmation request has been sent.'));
					}
					else {
						$session->addSuccess($this->__('Thank you for your subscription.'));
					}
				}
				catch (Mage_Core_Exception $e) {
					$session->addException($e, $this->__('There was a problem with the subscription: %s', $e->getMessage()));
				}
				catch (Exception $e) {
					$session->addException($e, $this->__('There was a problem with the subscription.'));
				}
				//$this->_redirectReferer();
			}
			else{
				if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
					$session   = Mage::getSingleton('core/session');
					$email     = (string) $this->getRequest()->getPost('email');

					try {
						if (!Zend_Validate::is($email, 'EmailAddress')) {
							Mage::throwException($this->__('Please enter a valid email address'));
						}

						$status = Mage::getModel('newsletter/subscriber')->subscribe($email);
						if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
							$session->addSuccess($this->__('Confirmation request has been sent'));
						}
						else {
							$session->addSuccess($this->__('Thank you for your subscription'));
						}
					}
					catch (Mage_Core_Exception $e) {
						$session->addException($e, $this->__('There was a problem with the subscription: %s', $e->getMessage()));
					}
					catch (Exception $e) {
						$session->addException($e, $this->__('There was a problem with the subscription'));
					}
				}
				//$this->_redirectReferer();			
			}
        }
        
    }
    public function updatebillingformAction()
    {   					
       $this->updatebillingform();
    }   
        
	public function updatesortbillingformAction()
    {   					
          $this->updatebillingform();
    } 
        
    // shipping change address
    public function updateshippingformAction()
    {   
    						
        $this->updateshippingform();
    } 
    
	public function updatesortshippingformAction()
    {   					
    	$this->updateshippingform();
    }
    
    public function updateshippingform()
    {
    	if ($this->getRequest()->isPost()) {        	
        	$postData=$this->getRequest()->getPost();    		
    		$customerAddressId = $postData['shipping_address_id'];     		        	
        		if(intval($customerAddressId)!=0){
	        		$postData = $this->filterdata($postData);
					if(version_compare(Mage::getVersion(),'1.4.0.1','>='))
						$data = $this->_filterPostData($postData);
					else
						$data=$postData;
	           	 	
	        	   //	if(isset($data['email'])) {
	                //	$data['email'] = trim($data['email']);
	            	//	}	            	          		
	            	$result = $this->getOnepage()->saveShipping($data, $customerAddressId); 
        		}        		       		
	    }	    
      	$this->loadLayout()->renderLayout();
    	
    }
    
	public function updatebillingform()
    {
    	 if ($this->getRequest()->isPost()) {        	
        	$postData=$this->getRequest()->getPost();    		
    		$customerAddressId = $postData['billing_address_id'];     		        	
        		if(intval($customerAddressId)!=0){
	        		$postData = $this->filterdata($postData);
					if(version_compare(Mage::getVersion(),'1.4.0.1','>='))
						$data = $this->_filterPostData($postData);
					else
						$data=$postData;
	           	 	
	        	   	if(isset($data['email'])) {
	                	$data['email'] = trim($data['email']);
	            		}	            	          		
	            	$result = $this->getOnepage()->saveBilling($data, $customerAddressId); 
        		}
        		else 
        		{
        			
        		}        		
	    }	    
      	$this->loadLayout()->renderLayout();
    }
    
    public function saveAddress($type,$data)
    {		 	
    		//	$save_in_address_book = $data['save_in_address_book'];			 	
			 	$addressId = $this->getRequest()->getPost($type.'_address_id');
			 
			 	if(isset($data['save_in_address_book']) && $data['save_in_address_book']==1 && $addressId != "")
			 	{			
			 				
			 			   // Save data				      
				            $customer = Mage::getSingleton('customer/session')->getCustomer();				          
				            $address  = Mage::getModel('customer/address');
				            
				            if ($addressId) {
				                $existsAddress = $customer->getAddressById($addressId);
				                if ($existsAddress->getId() && $existsAddress->getCustomerId() == $customer->getId()) {
				                    $address->setId($existsAddress->getId());
				                }	
				            $errors = array();				
				            /* @var $addressForm Mage_Customer_Model_Form */
				           $addressForm = Mage::getModel('customer/form');
				            $addressForm->setFormCode('customer_address_edit')
				               ->setEntity($address);
				            $addressData    = $this->getRequest()->getPost($type, array());//$addressForm->extractData($this->getRequest());
				            $addressErrors  = $addressForm->validateData($addressData);
				           
				            if ($addressErrors !== true) {
				                $errors = $addressErrors;
				            }
				
				            try {
				                $addressForm->compactData($addressData);
				                $address->setCustomerId($customer->getId())
				                    ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
				                    ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
				
				               // $addressErrors = $address->validate();
				                
				              //  if ($addressErrors !== true) {
				             //       $errors = array_merge($errors, $addressErrors);
				            //    }
				
				           //     if (count($errors) === 0) {
				                    $address->save();
				                    //$this->_getSession()->addSuccess($this->__('The address has been saved.'));
				                    //  $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
				                    // return;
				              //  } else {
				                	//die("toi");
		                  // 	Mage::getSingleton('customer/session')->setAddressFormData($this->getRequest()->getPost());
		                 // 	foreach ($errors as $errorMessage) {	                  		
		                 //    Mage::getSingleton('customer/session')->addError($errorMessage);		                     
		                 //    }
		                 //    Mage::log(Zend_Debug::dump($errors));	
		                    // die("toi");
				               }
				             catch (Mage_Core_Exception $e) {				            
				            	Mage::logException($e);				               
				            }
				   }
				
			 	}
	}
	
	protected function _expireAjax() {
		if (!Mage :: getSingleton('checkout/session')->getQuote()->hasItems()) {
			$this->getResponse()->setHeader('HTTP/1.1', '403 Session Expired')->setHeader('Login-Required', 'true')->sendResponse();
			die();
		}
	}
	public function voteAdd()
	{
		$pollId     = intval($this->getRequest()->getParam('poll_id'));
        $answerId   = intval($this->getRequest()->getParam('vote'));

        /** @var $poll Mage_Poll_Model_Poll */
        $poll = Mage::getModel('poll/poll')->load($pollId);

        /**
         * Check poll data
         */
        if ($poll->getId() && !$poll->getClosed() && !$poll->isVoted()) {
            $vote = Mage::getModel('poll/poll_vote')
                ->setPollAnswerId($answerId)
                ->setIpAddress(Mage::helper('core/http')->getRemoteAddr(true))
                ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());

            $poll->addVote($vote);
            Mage::getSingleton('core/session')->setJustVotedPoll($pollId);
            Mage::dispatchEvent(
                'poll_vote_add',
                array(
                    'poll'  => $poll,
                    'vote'  => $vote
                )
            );
        }
	}
	
}