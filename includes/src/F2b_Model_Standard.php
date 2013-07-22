<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package   F2b
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * F2b Checkout Module
 */
class F2b_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    //changing the payment to different from cc payment type and f2b payment type
    const PAYMENT_TYPE_AUTH = 'AUTHORIZATION';
    const PAYMENT_TYPE_SALE = 'SALE';

    protected $_code  = 'f2b_standard';
    protected $_formBlockType = 'f2b/standard_form';
    protected $_allowCurrencyCode = array('BRL');

     /**
     * Get f2b session namespace
     *
     * @return F2b_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('f2b/session');
    }

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Get current order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($this->getCheckout()->getLastRealOrderId());        
        return $order;
    }
	
    /**
     * Using internal pages for input payment data
     *
     * @return bool
     */
    public function canUseInternal()
    {
        return false;
    }

    /**
     * Using for multiple shipping address
     *
     * @return bool
     */
    public function canUseForMultishipping()
    {
        return false;
    }

    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('f2b/standard_form', $name)
            ->setMethod('f2b_standard')
            ->setPayment($this->getPayment())
            ->setTemplate('f2b/standard/form.phtml');

        return $block;
    }

    /*validate the currency code is avaialable to use for f2b or not*/
    public function validate()
    {
        parent::validate();
        $currency_code = $this->getQuote()->getBaseCurrencyCode();
        if (!in_array($currency_code,$this->_allowCurrencyCode)) {
            Mage::throwException(Mage::helper('f2b')->__('Selected currency code ('.$currency_code.') is not compatabile with F2b'));
        }
        return $this;
    }

    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
       return $this;
    }

    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {

    }

    public function canCapture()
    {
        return true;
    }

    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('f2b/standard/redirect', array('_secure' => true));
    }

	function getNumEndereco($endereco) {
    	$numEndereco = '';
    	$posSeparador = $this->getPosSeparador($endereco, false);  
    	if ($posSeparador !== false) {
			$numEndereco = trim(substr($endereco, $posSeparador + 1));
		}
      	$posComplemento = $this->getPosSeparador($numEndereco, true);
		if ($posComplemento !== false) {
			$numEndereco = trim(substr($numEndereco, 0, $posComplemento));
		}
		if ($numEndereco == '') {
			$numEndereco = '0';
		}
		return($numEndereco);
	}

	function getPosSeparador($endereco, $procuraEspaco = false) {
  	  $posSeparador = strpos($endereco, ',');
	  if ($posSeparador === false) {
	      $posSeparador = strpos($endereco, '-');
	  }
	  if ($procuraEspaco) {	  
	      if ($posSeparador === false) {
	          $posSeparador = strpos($endereco, ' ');
	      }
	  }
	  return($posSeparador);
	}

	function getUF($estado, $encoding) {
	
		if ($encoding == '') {
			$encoding = "UTF-8";
		}
	
		$sigla_uf = '';
		$nome_uf = mb_strtolower($estado, $encoding);

		if ($nome_uf == 'acre') {
		   $sigla_uf = 'AC';
		} else if ($nome_uf == 'alagoas') {
		   $sigla_uf = 'AL';
		} else if (($nome_uf == 'amapa') || ($nome_uf == mb_convert_encoding('amapá', $encoding))) {
		   $sigla_uf = 'AP';
		} else if ($nome_uf == 'amazonas') {
		   $sigla_uf = 'AM';
		} else if ($nome_uf == 'bahia') {
		   $sigla_uf = 'BA';
		} else if (($nome_uf == 'ceara') || ($nome_uf == mb_convert_encoding('ceará', $encoding))) {
		   $sigla_uf = 'CE';
		} else if ($nome_uf == 'distrito federal') {
		   $sigla_uf = 'DF';
		} else if ($nome_uf == 'espirito santo') {
		   $sigla_uf = 'ES';
		} else if (($nome_uf == 'goias') || ($nome_uf == mb_convert_encoding('goiás', $encoding))) {
		   $sigla_uf = 'GO';
		} else if (($nome_uf == 'maranhao') || ($nome_uf == mb_convert_encoding('maranhão', $encoding))) {
		   $sigla_uf = 'MA';
		} else if ($nome_uf == 'mato grosso') {
		   $sigla_uf = 'MT';
		} else if ($nome_uf == 'mato grosso do sul') {
		   $sigla_uf = 'MS';
		} else if ($nome_uf == 'minas gerais') {
		   $sigla_uf = 'MG';
		} else if (($nome_uf == 'para') || ($nome_uf == mb_convert_encoding('pará', $encoding))) {
		   $sigla_uf = 'PA';
		} else if (($nome_uf == 'paraiba') || ($nome_uf == mb_convert_encoding('paraíba', $encoding))) {
		   $sigla_uf = 'PB';
		} else if (($nome_uf == 'parana') || ($nome_uf == mb_convert_encoding('paraná', $encoding))) {
		   $sigla_uf = 'PR';
		} else if ($nome_uf == 'pernambuco') {
		   $sigla_uf = 'PE';
		} else if (($nome_uf == 'piaui') || ($nome_uf == mb_convert_encoding('piauí', $encoding))) {
		   $sigla_uf = 'PI';
		} else if ($nome_uf == 'rio de janeiro') {
		   $sigla_uf = 'RJ';
		} else if ($nome_uf == 'rio grande do norte') {
		   $sigla_uf = 'RN';
		} else if ($nome_uf == 'rio grande do sul') {
		   $sigla_uf = 'RS';
		} else if (($nome_uf == 'rondonia') || ($nome_uf == mb_convert_encoding('rondônia', $encoding))) {
		   $sigla_uf = 'RO';
		} else if ($nome_uf == 'roraima') {
		   $sigla_uf = 'RR';
		} else if ($nome_uf == 'santa catarina') {
		   $sigla_uf = 'SC';
		} else if (($nome_uf == 'sao paulo') || ($nome_uf == mb_convert_encoding('são paulo', $encoding))) {
		   $sigla_uf = 'SP';
		} else if ($nome_uf == 'sergipe') {
		   $sigla_uf = 'SE';
		} else if ($nome_uf == 'tocantins') {
		   $sigla_uf = 'TO';
		} else {
		   $sigla_uf = $nome_uf;
		}	  
		
		return($sigla_uf);
	}
	
	public function getStandardCheckoutFormFields() {
	
        $quote = $this->getQuote();
       	$currency_code = $this->getQuote()->getBaseCurrencyCode();

        $order = $this->getOrder();
        $a = $order->getBillingAddress();
		
		$cep = substr(eregi_replace ("[^0-9]", "", $a->getPostcode()).'00000000',0,8);
        // removes non numeric characters from the telephone field, and trims to 8 chars long.
        
        $cust_ddd = '00';    	
        $cust_telephone = eregi_replace ("[^0-9]", "", $a->getTelephone());
        $st = strlen($cust_telephone)-8;
        if ($st>0) {
            $cust_ddd = substr($cust_telephone, 0, 2);
	    	$cust_telephone = substr($cust_telephone, $st, 8);
        }

		$vencimento = date('Y-m-d',time()+$this->getConfigData('prazo_vencimento')*24*60*60);
		
		$pos_numero = strpos($a->getStreet(1), $this->getNumEndereco($a->getStreet(1)));
		$encoding_config = $this->getConfigData('encoding');
		if ($encoding_config == '') {
			$encoding_config = "UTF-8";
		}
		
       	$sArr = array(
            'conta'        		=>  $this->getConfigData('conta'),
            'tipo_ws'   		=> "Magento",
            'encoding'			=>  $encoding_config,
            'taxa'				=>  $this->getConfigData('taxa'),
            'tipo_taxa'			=>  $this->getConfigData('tipo_taxa'),
            'tipo_cobranca'		=>  $this->getConfigData('tipo_cobranca'),
            'vencimento'		=>  $vencimento,
    	    'num_document'     	=> $this->getCheckout()->getLastRealOrderId(),
            'demonstrativo_1'	=>  $this->getConfigData('demonstrativo_1'),
            'demonstrativo_2'	=>  $this->getConfigData('demonstrativo_2'),
            'demonstrativo_3'	=>  $this->getConfigData('demonstrativo_3'),
            'nome'      		=> $a->getFirstname().' '.$a->getLastname(),
            'email_1'    		=> $order->getCustomerEmail(),
            'envio'				=> $this->getConfigData('envio'),
            'endereco_logradouro' => substr($a->getStreet(1), 0, $this->getPosSeparador($a->getStreet(1), false)),
            'endereco_numero'   => $this->getNumEndereco($a->getStreet(1)),
            'endereco_complemento' => substr($a->getStreet(1), $pos_numero+1+strlen($this->getNumEndereco($a->getStreet(1)))),
            'endereco_bairro' 	=> $a->getStreet(2),
            'endereco_cidade'	=> $a->getCity(),
            'endereco_estado'  	=> $this->getUF($a->getRegionCode(), $encoding_config),
            'endereco_cep' 		=> $cep,
            'telefone_ddd'      => $cust_ddd,
            'telefone_numero'  	=> $cust_telephone,
        );
            
		
		$items = $this->getQuote()->getAllItems();

		$valor_total = 0.00;
		
        if ($items) {
            $i = 1;
            foreach($items as $item) {
                $valorProduto = ($item->getBaseCalculationPrice() - $item->getBaseDiscountAmount());
				$valorProduto = str_replace(',', '.', $valorProduto);
				
				$j = $i + 3;
				$sArr = array_merge($sArr, array(
                    'demonstrativo_'.$j   => $item->getQty() . ' ' . $item->getName() . ' - ' . $item->getSku(),
			    ));
				$i++;
			}
        }

		$valor_total = sprintf('%.2f', $order->getGrandTotal()); 		
		
		$sArr = array_merge($sArr, array(
			'valor' => $valor_total,
		));
		
        $sReq = '';
        $rArr = array();
        foreach ($sArr as $k=>$v) {
            /*
            replacing & char with and. otherwise it will break the post
            */
            $value =  str_replace("&","and",$v);
            $rArr[$k] =  $value;
            $sReq .= '&'.$k.'='.$value;
        }

        if ($this->getDebug() && $sReq) {
            $sReq = substr($sReq, 1);
            $debug = Mage::getModel('f2b/api_debug')
                    ->setApiEndpoint($this->getF2bUrl())
                    ->setRequestBody($sReq)
                    ->save();
        }
		
        return $rArr;
    }

    //  define a url do f2b
    public function getF2bUrl()
    {
         $url='https://www.f2b.com.br/BillingWeb';
         return $url;
    }

    public function getDebug()
    {
        return Mage::getStoreConfig('f2b/wps/debug_flag');
    }

    /**
     * Instantiate state and set it to state object
     * @param string $paymentAction
     * @param Varien_Object
     */
    public function initialize($paymentAction, $stateObject)
    {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus('pending_payment');
        $stateObject->setIsNotified(false);
    }
	


}
