<?php
/**
 * Magento F2b Payment Modulo
 *
 * @category   Mage
 * @package    Mage_F2b
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * F2b Payment Action Dropdown source
 *
 */
class Mage_F2b_Model_Source_StandardAction
{
    public function toOptionArray()
    {
        return array(
            array('value' => Mage_F2b_Model_Standard::PAYMENT_TYPE_AUTH, 'label' => Mage::helper('F2b')->__('Authorization')),
            array('value' => Mage_F2b_Model_Standard::PAYMENT_TYPE_SALE, 'label' => Mage::helper('F2b')->__('Sale')),
        );
    }
}