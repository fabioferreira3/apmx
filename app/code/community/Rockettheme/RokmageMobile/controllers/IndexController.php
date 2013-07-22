<?php

class Rockettheme_RokmageMobile_IndexController extends Mage_Core_Controller_Front_Action
{
    public function toMobileAction() {
        if (isset($_SESSION['rokmagemobileswitcher'])) unset($_SESSION['rokmagemobileswitcher']);
        return $this->_redirect('/');
    }
    
    public function toDesktopAction() {
        $_SESSION['rokmagemobileswitcher'] = true;
        return $this->_redirect('/');
    }
    
}