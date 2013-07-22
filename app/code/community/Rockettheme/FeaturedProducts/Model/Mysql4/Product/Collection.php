<?php
/**
 * @version   1.6.2.0 March 14, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class Rockettheme_FeaturedProducts_Model_Mysql4_Product_Collection extends Mage_Reports_Model_Mysql4_Product_Collection    
{
	/**
	 * All Products - load a full product collection
	 */
	public function filterbyFullCollection($attribute,$visibility,$_category,$no_of_items,$randomize,$_currcat)
	{
		// Check if root filter is ON
		$filterbyroot = (int)Mage::getStoreConfig('featured_products/settings/filterbyroot');
		// Get collection
	    if ($filterbyroot == 0) { $this->addAttributeToSelect($attribute)->addAttributeToFilter('visibility', $visibility)->addCategoryFilter($_category)->addUrlRewrite()->addFinalPrice()->setPageSize($no_of_items)->getSelect()->order($randomize); }
	    else {
	    $this->addAttributeToSelect($attribute)->addAttributeToFilter('visibility', $visibility)->addCategoryFilter($_category)->addUrlRewrite()->addFinalPrice()->joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'left')->addAttributeToFilter('category_id', array('in' => $_currcat))->setPageSize($no_of_items)->getSelect()->order($randomize); }
	    return $this;
	}
	/**
	 * Best-Selling - load a product collection filtered by best-selling
	 */
	public function filterbyBestSelling($attribute,$visibility,$_category,$no_of_items,$_currcat)
	{
	    // Check if root filter is ON
		$filterbyroot = (int)Mage::getStoreConfig('featured_products/settings/filterbyroot');
		// Get collection
	    if ($filterbyroot == 0) { $this->addAttributeToSelect($attribute)->addOrderedQty()->setOrder('ordered_qty', 'desc')->addAttributeToFilter('visibility', $visibility)->addCategoryFilter($_category)->addUrlRewrite()->addFinalPrice()->setPageSize($no_of_items); }
	    else {
	    $this->addAttributeToSelect($attribute)->addOrderedQty()->setOrder('ordered_qty', 'desc')->addAttributeToFilter('visibility', $visibility)->addCategoryFilter($_category)->addUrlRewrite()->addFinalPrice()->joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'left')->addAttributeToFilter('category_id', array('in' => $_currcat))->setPageSize($no_of_items); }
	    return $this;
	}
	/**
	 * Most Viewed - load a product collection filtered by most viewed
	 */
	public function filterbyMostViewed($attribute,$visibility,$_category,$no_of_items,$_currcat)
	{
	    // Check if root filter is ON
		$filterbyroot = (int)Mage::getStoreConfig('featured_products/settings/filterbyroot');
		// Get collection
	    if ($filterbyroot == 0) { $this->addAttributeToSelect($attribute)->addViewsCount()->setOrder('views_count', 'desc')->addAttributeToFilter('visibility', $visibility)->addCategoryFilter($_category)->addUrlRewrite()->addFinalPrice()->setPageSize($no_of_items); }
	    else {
	    $this->addAttributeToSelect($attribute)->addViewsCount()->setOrder('views_count', 'desc')->addAttributeToFilter('visibility', $visibility)->joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'left')->addAttributeToFilter('category_id', array('in' => $_currcat))->addCategoryFilter($_category)->addUrlRewrite()->addFinalPrice()->setPageSize($no_of_items); }
	    return $this;
	}
	/**
	 * Featured ATTRIBUTE - load a product collection filtered by featured ATTRIBUTE
	 */
	public function filterbyFeaturedAttribute($attribute,$visibility,$featuredattribute,$_category,$no_of_items,$randomize,$_currcat)
	{
	    // Check if root filter is ON
		$filterbyroot = (int)Mage::getStoreConfig('featured_products/settings/filterbyroot');
		// Get collection
	    if ($filterbyroot == 0) { $this->addAttributeToSelect($attribute)->addAttributeToFilter('visibility', $visibility)->addAttributeToFilter($featuredattribute, 1)->addCategoryFilter($_category)->addUrlRewrite()->addFinalPrice()->setPageSize($no_of_items)->getSelect()->order($randomize); }
	    else {
	    $this->addAttributeToSelect($attribute)->addAttributeToFilter('visibility', $visibility)->addAttributeToFilter($featuredattribute, 1)->addCategoryFilter($_category)->addUrlRewrite()->addFinalPrice()->joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'left')->addAttributeToFilter('category_id', array('in' => $_currcat))->setPageSize($no_of_items)->getSelect()->order($randomize); }
	    return $this;
	}
	/**
	 * Featured CATEGORY - load a product collection filtered by featured CATEGORY
	 */
	public function filterbyFeaturedCategory($attribute,$visibility,$_featcategory,$no_of_items,$randomize,$_currcat)
	{
	    // Check if root filter is ON
		$filterbyroot = (int)Mage::getStoreConfig('featured_products/settings/filterbyroot');
		// Get collection
	    if ($filterbyroot == 0) { $this->addAttributeToSelect($attribute)->addAttributeToFilter('visibility', $visibility)->addCategoryFilter($_featcategory)->addUrlRewrite()->addFinalPrice()->setPageSize($no_of_items)->getSelect()->order($randomize); }
	    else {
	    $this->addAttributeToSelect($attribute)->addAttributeToFilter('visibility', $visibility)->addCategoryFilter($_featcategory)->addUrlRewrite()->addFinalPrice()->joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'left')->addAttributeToFilter('category_id', array('in' => $_currcat))->setPageSize($no_of_items)->getSelect()->order($randomize); }
	    return $this;
	}
	/**
	 * Recently Added - load a product collection filtered by recently added
	 */
	public function filterbyRecentlyAdded($attribute,$visibility,$_category,$no_of_items,$_currcat)
	{
	    // Check if root filter is ON
		$filterbyroot = (int)Mage::getStoreConfig('featured_products/settings/filterbyroot');
		// Get collection
	    if ($filterbyroot == 0) { $this->addAttributeToSelect($attribute)->addAttributeToFilter('visibility', $visibility)->addCategoryFilter($_category)->addUrlRewrite()->addFinalPrice()->setOrder('created_at', 'desc')->setPageSize($no_of_items); }
	    else {
	    $this->addAttributeToSelect($attribute)->addAttributeToFilter('visibility', $visibility)->addCategoryFilter($_category)->addUrlRewrite()->addFinalPrice()->setOrder('created_at', 'desc')->joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'left')->addAttributeToFilter('category_id', array('in' => $_currcat))->setPageSize($no_of_items); }
	    return $this;
	}
	/**
	 * Highest Rated - load a product collection filtered by ratings
	 */
	public function filterbyHighestRated($attribute,$visibility,$_category,$no_of_items,$_currcat)
	{
		$tableName = Mage::getSingleton('core/resource')->getTableName('review_entity_summary');

	    // Check if root filter is ON
		$filterbyroot = (int)Mage::getStoreConfig('featured_products/settings/filterbyroot');
		// Get collection
	    if ($filterbyroot == 0) { $this->joinField('rating_summary', $tableName, 'rating_summary', 'entity_pk_value=entity_id', array('entity_type'=>1, 'store_id'=> Mage::app()->getStore()->getId()), 'left')->addAttributeToSelect($attribute)->addAttributeToFilter('visibility', $visibility)->addCategoryFilter($_category)->addUrlRewrite()->addFinalPrice()->setOrder('rating_summary', 'desc')->setPageSize($no_of_items); }
	    else {
	    $this->joinField('rating_summary', $tableName, 'rating_summary', 'entity_pk_value=entity_id', array('entity_type'=>1, 'store_id'=> Mage::app()->getStore()->getId()), 'left')->addAttributeToSelect($attribute)->addAttributeToFilter('visibility', $visibility)->addCategoryFilter($_category)->addUrlRewrite()->addFinalPrice()->setOrder('rating_summary', 'desc')->joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'left')->addAttributeToFilter('category_id', array('in' => $_currcat))->setPageSize($no_of_items); }
	    return $this;
	}
	/**
	 * Special Price - load a collection of products currently with Special Price
	 */
	public function filterbySpecialPrice($attribute,$visibility,$_category,$no_of_items,$_currcat)
	{
		$todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

	    // Check if root filter is ON
		$filterbyroot = (int)Mage::getStoreConfig('featured_products/settings/filterbyroot');
		// Get collection
	    if ($filterbyroot == 0) { $this->addAttributeToSelect($attribute)->addAttributeToFilter('visibility', $visibility)->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $todayDate))
				            ->addAttributeToFilter('special_to_date', array('or'=> array(
				                0 => array('date' => true, 'from' => $todayDate),
				                1 => array('is' => new Zend_Db_Expr('null')))
				            ), 'left')
				            ->addAttributeToSort('special_from_date', 'desc')->addCategoryFilter($_category)->addUrlRewrite()->addFinalPrice()->setPageSize($no_of_items); }
	    else {
		$this->addAttributeToSelect($attribute)->addAttributeToFilter('visibility', $visibility)->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $todayDate))
				            ->addAttributeToFilter('special_to_date', array('or'=> array(
				                0 => array('date' => true, 'from' => $todayDate),
				                1 => array('is' => new Zend_Db_Expr('null')))
				            ), 'left')
				            ->addAttributeToSort('special_from_date', 'desc')->addCategoryFilter($_category)->addUrlRewrite()->addFinalPrice()->joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'left')->addAttributeToFilter('category_id', array('in' => $_currcat))->setPageSize($no_of_items); }
	    return $this;
	}

}
