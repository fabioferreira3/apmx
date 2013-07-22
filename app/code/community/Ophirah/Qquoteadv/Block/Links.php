<?php
class Ophirah_Qquoteadv_Block_Links extends Mage_Core_Block_Template
{
    /**
     * Add Quote link to parent block
     *
     * @return Ophirah_Qquoteadv_Block_Links
     */
    public function addQuoteLink()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock && Mage::helper('core')->isModuleOutputEnabled('Ophirah_Qquoteadv')) {
        
        		$count  = Mage::helper('qquoteadv')->getTotalQty();
            if ($count == 1) {
                $text = $this->__('My Quote (%s item)', $count);
            } elseif ($count > 0) {
                $text = $this->__('My Quote (%s items)', $count);
            } else {
                $text = $this->__('My Quote');
            }

            $parentBlock->addLink($text, 'qquoteadv/index', $text, true, array(), 50, null, 'class="top-link-qquoteadv"');
        }
        return $this;
    }
}