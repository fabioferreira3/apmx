<?php

// Some of the following code is based on the excellent Magento Custom Menu 
// http://web-experiment.info/webandpeople-custom-menu-extension.html
// and is licensed under the Creative Commons Attribution 3.0 Unported License 
// http://creativecommons.org/licenses/by/3.0/

class Rockettheme_MageMenus_Block_Navigation extends Mage_Catalog_Block_Navigation
{
    const CUSTOM_BLOCK_TEMPLATE = 'rokmage_megamenu_%d';

    public function getRokMageMegaMenu($category, $level = 0, $last = false)
    {
        if (!$category->getIsActive()) return '';

        $html = array();
        $blocksenabled = (int)Mage::getStoreConfig('mage_menus/settings_megamenu/blocksenabled');
        $id = $category->getId();
        // --- Static Block ---
        $blockId = sprintf('rokmage_megamenu_%d', $id); // --- static block key
        $blockHtml = $this->getLayout()->createBlock('cms/block')->setBlockId($blockId)->toHtml();
        // --- Sub Categories ---
        $activeChildren = $this->getActiveChildren($category, $level);
        // --- class for active category ---
        $active = ''; if ($this->isCategoryActive($category)) $active = ' active';
        // --- Popup functions for show ---
        $drawPopup = ($blockHtml || count($activeChildren));
        if ($drawPopup)
        {
            $html[] = '<li id="menu' . $id . '" class="level0 parent' . $active . '">';
        }
        else
        {
            $html[] = '<li id="menu' . $id . '" class="level0' . $active . '">';
        }
        // --- Top Menu Item ---
        $html[] = '<a href="'.$this->getCategoryUrl($category).'">';
        $name = $this->escapeHtml($category->getName());
        $name = str_replace(' ', '&nbsp;', $name);
        $html[] = '<span>' . $name . '</span>';
        $html[] = '</a>';
        // --- Add Popup block (hidden) ---
        if ($drawPopup)
        {
            // --- Popup function for hide ---
            $popupwidth = (int)Mage::getStoreConfig('mage_menus/settings_megamenu/width');
            $html[] = '<ul id="popup' . $id . '" class="popup" style="width:' . $popupwidth . 'px"><li id="inner' . $id . '">';
            // --- draw Sub Categories ---
            if (count($activeChildren))
            {
                $html[] = '<div class="block-cats">';
                $html[] = $this->drawColumns($activeChildren);
                $html[] = '<div class="clearBoth"></div>';
                $html[] = '</div>';
            }
            // --- draw Custom User Block ---
            if ($blocksenabled && $blockHtml)
            {
                $html[] = '<div class="block-custom">';
                $html[] = $blockHtml;
                $html[] = '</div>';
            }
            $html[] = '</li></ul>';
            $html[] = '</li>';
        }

        $html = implode("\n", $html);
        return $html;
    }

    public function drawColumns($children)
    {
        $html = '';
        $colswidth = (int)Mage::getStoreConfig('mage_menus/settings_megamenu/colswidth');
        // --- explode by columns ---
        $columns = (int)Mage::getStoreConfig('mage_menus/settings_megamenu/cols');
        if ($columns < 1) $columns = 1;
        $chunks = $this->explodeByColumns($children, $columns);
        // --- draw columns ---
        foreach ($chunks as $key => $value)
        {
            if (!count($value)) continue;
            $html.= '<div class="column" style="width:' . $colswidth . 'px">';
            $html.= $this->drawMenuItem($value, 1);
            $html.= '</div>';
        }
        return $html;
    }

    protected function getActiveChildren($parent, $level)
    {
        $activeChildren = array();
        // --- check level ---
        $maxLevel = (int)Mage::getStoreConfig('mage_menus/settings_megamenu/subcats');
        if ($maxLevel > 0)
        {
            if ($level >= ($maxLevel - 1)) return $activeChildren;
        }
        // --- check level ---
        if (Mage::helper('catalog/category_flat')->isEnabled())
        {
            $children = $parent->getChildrenNodes();
            $childrenCount = count($children);
        }
        else
        {
            $children = $parent->getChildren();
            $childrenCount = $children->count();
        }
        $hasChildren = $children && $childrenCount;
        if ($hasChildren)
        {
            foreach ($children as $child)
            {
                if ($child->getIsActive())
                {
                    array_push($activeChildren, $child);
                }
            }
        }
        return $activeChildren;
    }

    private function explodeByColumns($target, $num)
    {
        $count = count($target);
        if ($count) $target = array_chunk($target, ceil($count / $num));
        $target = array_pad($target, $num, array());
        return $target;  
    }

    private function _countChild($children, $level, &$count)
    {
        foreach ($children as $child)
        {
            if ($child->getIsActive())
            {
                $count++; $activeChildren = $this->getActiveChildren($child, $level);
                if (count($activeChildren) > 0) $this->_countChild($activeChildren, $level + 1, $count);
            }
        }
    }

    public function drawMenuItem($children, $level = 1)
    {
        $html = '<div class="itemMenu level' . $level . '">';
        $keyCurrent = $this->getCurrentCategory()->getId();
        foreach ($children as $child)
        {
            if ($child->getIsActive())
            {
                // --- class for active category ---
                $active = '';
                if ($this->isCategoryActive($child))
                {
                    $active = ' active';
                    if ($child->getId() == $keyCurrent) $active = ' active';
                }
                // --- format category name ---
                $name = $this->escapeHtml($child->getName());
                $name = str_replace(' ', '&nbsp;', $name);
                $html.= '<a class="itemMenuName level' . $level . $active . '" href="' . $this->getCategoryUrl($child) . '"><span>' . $name . '</span></a>';
                $activeChildren = $this->getActiveChildren($child, $level);
                if (count($activeChildren) > 0)
                {
                    $html.= '<div class="itemSubMenu level' . $level . '">';
                    $html.= $this->drawMenuItem($activeChildren, $level + 1);
                    $html.= '</div>';
                }
            }
        }
        $html.= '</div>';
        return $html;
    }

    // Add accordion menu

    public function showAccordionMenu( $category, $object )
    {
       
        // Check if item count to be shown
        $itemcount = (int)Mage::getStoreConfig('mage_menus/settings_sideleft/itemcount');
        echo '<a href="' . $object->getCategoryUrl( $category ) . '" class="mageside-menu-heading ' . ($object->isCategoryActive( $category ) ? 'activecurrent current' : '' ) . '">';
        echo '<span ';
        if(Mage::helper('catalog/category_flat')->isEnabled()) { 
            if ( ( $children = $category->getChildrenNodes() ) && count($children) )
            {
            echo 'class="parent"';
            }
        }   
        else
        {
            if ( ( $children = $category->getChildren() ) && $children->count() )
            {
                echo 'class="parent"';
            }
        };
        echo '>';
        echo $object->htmlEscape( $category->getName() );
        echo '</span></a>';
        $category_object = Mage::getModel('catalog/category')->load($category->getId()); // If you don't already have one
        $total = Mage::getModel('catalog/layer')->setCurrentCategory( $category_object )->getProductCollection()->getSize();
             
        if(Mage::helper('catalog/category_flat')->isEnabled()) { 
            if ( ( $children = $category->getChildrenNodes() ) && count($children) )
            {
                echo '<div class="mageside-menu-toggle-button' . ($object->isCategoryActive( $category ) ? '-current' : '' ) . '"></div><div class="mageside-menu-toggle-container' . ($object->isCategoryActive( $category ) ? ' active' : '' ) . '"><ul>';
                foreach ( $children as $child_category)
                    $this->showCategoryTree( $child_category, $object );
                echo '</ul></div>';
            }
        }   
        else
            {
                if ( ( $children = $category->getChildren() ) && $children->count() )
                {
                    echo '<div class="mageside-menu-toggle-button' . ($object->isCategoryActive( $category ) ? '-current' : '' ) . '"></div><div class="mageside-menu-toggle-container' . ($object->isCategoryActive( $category ) ? ' active' : '' ) . '"><ul>';
                    foreach ( $children as $child_category)
                        $this->showCategoryTree( $child_category, $object );
                    echo '</ul></div>';
                }
            }
    }

    // Add category tree menu

    public function showCategoryTree( $category, $object )
    {
       
        // Check if item count to be shown
        $itemcount = (int)Mage::getStoreConfig('mage_menus/settings_sideleft/itemcount');
        // Get category level
        $catlevel = $category->getLevel();
        // Get levels to show from admin
        $catalog_cats = (int)Mage::getStoreConfig('mage_menus/settings_sideleft/catalog_cats');
        if($catalog_cats == 'normal' || $catalog_cats == 'tree'){ $visiblelevel = (int)Mage::getStoreConfig('mage_menus/settings_sideleft/subcats'); }
        else{ $visiblelevel = (int)Mage::getStoreConfig('mage_menus/settings_sideright/subcats'); };
        echo '<li>';
        echo '<a href="' . $object->getCategoryUrl( $category ) . '"' . ($object->isCategoryActive( $category ) ? 'class="activecurrent current"' : '' ) . '>';
        echo $object->htmlEscape( $category->getName() );
        echo '</a>';
        $category_object = Mage::getModel('catalog/category')->load($category->getId()); // If you don't already have one
        $total = Mage::getModel('catalog/layer')->setCurrentCategory( $category_object )->getProductCollection()->getSize();
        if($itemcount == 1) { 
            echo "<span class=\"mageside-prod-num\">&nbsp;(".$total.")</span>";
            };
             
        if(Mage::helper('catalog/category_flat')->isEnabled()) { 
            if ( ( $children = $category->getChildrenNodes() ) && count($children) && $catlevel <= $visiblelevel )
            {
            echo '<ul>';
            foreach ( $children as $child_category)
                $this->showCategoryTree( $child_category, $object );
            echo '</ul>';
            }
        }   
        else
            {
                if ( ( $children = $category->getChildren() ) && $children->count() && $catlevel <= $visiblelevel )
                {
                    echo '<ul>';
                    foreach ( $children as $child_category)
                        $this->showCategoryTree( $child_category, $object );
                    echo '</ul>';
                }
            }

        echo '</li>';
    }

    // Add custom menu for mobile theme

    public function showMobileTree( $category, $object )
    {       
        // Check if item count to be shown
        $itemcount = (int)Mage::getStoreConfig('rokmage_mobile/menu_settings/show_count');
        // Get category level
        $catlevel = $category->getLevel();
        // Get levels to show from admin
        $visiblelevel = (int)Mage::getStoreConfig('rokmage_mobile/menu_settings/subcats');
        echo '<li ';
        if($catlevel == '2'){ echo 'data-theme="a"';} else { echo 'data-theme="b"';};
        echo 'class="level'.$catlevel.'">';
        echo '<a href="' . $object->getCategoryUrl( $category ) . '"' . ($object->isCategoryActive( $category ) ? 'class="active"' : '' ) . '>';
        echo $object->htmlEscape( $category->getName() );
        
        $category_object = Mage::getModel('catalog/category')->load($category->getId()); // If you don't already have one
        $total = Mage::getModel('catalog/layer')->setCurrentCategory( $category_object )->getProductCollection()->getSize();
        if($itemcount == 1) { 
            echo '<span class="numcount ';
            if($catlevel == '2') { echo 'ui-btn-up-a'; } else { echo 'ui-btn-up-b'; };
            echo ' ui-btn-corner-all">'.$total.'</span>';
        };
        echo '</a>';
        echo '</li>';
                 
        if(Mage::helper('catalog/category_flat')->isEnabled()) { 
            if ( ( $children = $category->getChildrenNodes() ) && count($children) && $catlevel <= $visiblelevel )
            {
            foreach ( $children as $child_category)
                $this->showMobileTree( $child_category, $object );
            }
        }   
        else
            {
            if ( ( $children = $category->getChildren() ) && $children->count() && $catlevel <= $visiblelevel )
            {
            foreach ( $children as $child_category)
                $this->showMobileTree( $child_category, $object );
            }
       }   
    }
}

