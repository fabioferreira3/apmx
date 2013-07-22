<?php

class Rockettheme_RokmageMobile_Model_Package extends Mage_Core_Model_Design_Package
{
    protected function _checkUserAgentAgainstRegexps($regexpsConfigPath)
    {
        if (isset($_SESSION['rokmagemobileswitcher']) && $_SESSION['rokmagemobileswitcher']) {
            return false;
        }
        return parent::_checkUserAgentAgainstRegexps($regexpsConfigPath);
    }
}