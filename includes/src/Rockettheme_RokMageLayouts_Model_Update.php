<?php

class Rockettheme_RokMageLayouts_Model_Update extends Mage_Core_Model_Layout_Update
{
		public function getFileLayoutUpdatesXml($area, $package, $theme, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = Mage::app()->getStore()->getId();
        }
        /* @var $design Mage_Core_Model_Design_Package */
        $design = Mage::getSingleton('core/design_package');
        $layoutXml = null;
        $elementClass = $this->getElementClass();
        $updatesRoot = Mage::app()->getConfig()->getNode($area.'/layout/updates');
        Mage::dispatchEvent('core_layout_update_updates_get_after', array('updates' => $updatesRoot));
        $updateFiles = array();
        foreach ($updatesRoot->children() as $updateNode) {
            if ($updateNode->file) {
                $module = $updateNode->getAttribute('module');
                if ($module && Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $module, $storeId)) {
                    continue;
                }
                $updateFiles[] = (string)$updateNode->file;
            }
        }
        // custom local layout updates file - load always last

// ------------------------ RokMage overrides

		// Get store name
			$storeName = Mage::app()->getStore()->getCode();

		// Check if overrides enabled
			$configData = Mage::getStoreConfig('rokmage_layouts');
			$enable = $configData['general']['enable'];

		// Set default value
			$local = 'local.xml';
			
if($enable == 1) {
	
		// Check our route
			$route = Mage::app()->getFrontController()->getRequest()->getRouteName();
			if(Mage::registry('current_product')) {	$route = "product"; };
			
		//	echo "ROUTE IS ".$route;
		
		// Check which local.xml to load
			$configData = Mage::getStoreConfig('rokmage_layouts');
			$localoverride = $configData['general']['localoverride'];
			
		// If Product page, check which local.xml to load 
			if($route == 'product') {
			$configData = Mage::getStoreConfig('product_view');
			$localoverride = $configData['general']['localoverride']; };
			
		// If Catalog page, check which local.xml to load 
			if($route == 'catalog') {
			$configData = Mage::getStoreConfig('category_view');
			$localoverride = $configData['general']['localoverride']; };
			
		if($localoverride == 1) {
			$local = 'local2columns.xml'; 
			} else {
        	$local = 'local3columns.xml'; };
};

		$updateFiles[] = $local; 
		array_push($updateFiles, 'local.xml'); // Load standard local.xml for any custom updates

		// Get admin layout updates
		$configData = Mage::getStoreConfig('rokmage_layouts');
		$adminlocal = $configData['general']['local'];
		if($adminlocal != '') { // If entries in admin
			$tag = '<?xml version="1.0"?'.'>';
			$adminlocal = htmlspecialchars_decode($adminlocal);
			$file = $tag.$adminlocal;
			file_put_contents('app/design/frontend/base/default/layout/'.$storeName.'_local.xml', $file);
			array_push($updateFiles, $storeName.'_local.xml'); // Load admin defined storename_local.xml last for any custom updates
		};
		
// ------------------------ End RokMage overrides
		
        $layoutStr = '';
        foreach ($updateFiles as $file) {
            $filename = $design->getLayoutFilename($file, array(
                '_area'    => $area,
                '_package' => $package,
                '_theme'   => $theme
            ));
            if (!is_readable($filename)) {
                continue;
            }
            $fileStr = file_get_contents($filename);
            $fileStr = str_replace($this->_subst['from'], $this->_subst['to'], $fileStr);
            $fileXml = simplexml_load_string($fileStr, $elementClass);
            if (!$fileXml instanceof SimpleXMLElement) {
                continue;
            }
            $layoutStr .= $fileXml->innerXml();
        }
        $layoutXml = simplexml_load_string('<layouts>'.$layoutStr.'</layouts>', $elementClass);
        return $layoutXml;
    }
}