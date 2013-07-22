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
 * @package    F2b
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * F2bStandard Checkout Controller
 */
class F2b_StandardController extends Mage_Core_Controller_Front_Action
{
    /**
     * Order instance
     */
    protected $_order;

    /**
     *  Get order
     *
     *  @param    none
     *  @return	  Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->_order == null) {
        }
        return $this->_order;
    }

    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    /**
     * Get singleton with f2b strandard order transaction information
     *
     * @return F2b_Model_Standard
     */
    public function getStandard()
    {
        return Mage::getSingleton('f2b/standard');
    }

    /**
     * When a customer chooses F2b on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setF2bStandardQuoteId($session->getQuoteId());
        $this->getResponse()->setBody($this->getLayout()->createBlock('f2b/standard_redirect')->toHtml());
        $session->unsQuoteId();
    }

    /**
     * When a customer cancel payment from f2b.
     */
    public function cancelAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getF2bStandardQuoteId(true));

        // cancel order
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {
                $order->cancel()->save();
            }
        }

        $this->_redirect('checkout/cart');
     }

    /**
     * when f2b returns
     * The order information at this point is in POST
     * variables.  However, you don't want to "process" the order until you
     * get validation from the IPN.
     */
    public function  successAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getF2bStandardQuoteId(true));
        /**
         * set the quote as inactive after back from f2b
         */
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();

        /**
         * send confirmation email to customer
         */
        $order = Mage::getModel('sales/order');

        $order->load(Mage::getSingleton('checkout/session')->getLastOrderId());
        if($order->getId()){
            $order->sendNewOrderEmail();
        }

        //Mage::getSingleton('checkout/session')->unsQuoteId();

        $this->_redirect('checkout/onepage/success', array('_secure'=>true));
    }

    /**
     * when f2b returns via ipn
     * cannot have any output here
     * validate IPN data
     * if data is valid need to update the database that the user has
     */
    public function ipnAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_redirect('');
            return;
        }

        if($this->getStandard()->getDebug()){
            $debug = Mage::getModel('f2b/api_debug')
                ->setApiEndpoint($this->getStandard()->getF2bUrl())
                ->setRequestBody(print_r($this->getRequest()->getPost(),1))
                ->save();
        }

        $this->getStandard()->setIpnFormData($this->getRequest()->getPost());
        $this->getStandard()->ipnPostSubmit();
    }
}
