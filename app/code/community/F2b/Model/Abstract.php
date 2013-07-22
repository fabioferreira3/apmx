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
 *
 * F2b Abstract Payment Module
 */
abstract class F2b_Model_Abstract extends Mage_Payment_Model_Method_Abstract
{
    /**
     * Get F2b API Model
     *
     * @return F2b_Model_Api_Nvp
     */
    public function getApi()
    {
        return Mage::getSingleton('f2b/api_nvp');
    }

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

    public function getRedirectUrl()
    {
        return $this->getApi()->getRedirectUrl();
    }

    public function getCountryRegionId()
    {
        $a = $this->getApi()->getShippingAddress();
        return $this;
    }
}
