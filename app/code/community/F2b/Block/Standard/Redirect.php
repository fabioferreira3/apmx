<?php

class F2b_Block_Standard_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $standard = Mage::getModel('f2b/standard');

        $form = new Varien_Data_Form();
        $form->setAction($standard->getF2bUrl())
            ->setId('f2b_standard_checkout')
            ->setName('f2b_standard_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);

        foreach ($standard->getStandardCheckoutFormFields() as $field=>$value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }
        $html  = '<html>';
		$html .= '<body>';
        $html .= $this->__('Você será redirecionado para a F2b em alguns instantes.');
        $html .= $form->toHtml();
        $html .= '<script type="text/javascript">document.getElementById("f2b_standard_checkout").submit();</script>';
        $html .= '</body></html>';

        return $html;
    }
}