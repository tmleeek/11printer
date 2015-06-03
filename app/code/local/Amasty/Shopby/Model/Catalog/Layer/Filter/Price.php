<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Shopby_Model_Catalog_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Price
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Retrieves resource instance
     */
    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            if (Mage::helper('amshopby')->isVersionLessThan(1, 4)){
                $this->_resource = Mage::getSingleton('amshopby/mysql4_price13');
                $this->_resource->setCustomerGroupId(Mage::getSingleton('customer/session')->getCustomerGroupId());
                $this->_resource->setRate(Mage::app()->getStore()->getCurrentCurrencyRate());
                $this->_resource->setStoreId(Mage::app()->getStore()->getId());
            }
            else {
                $this->_resource = Mage::getModel('amshopby/mysql4_price');
            }
        }
        
        return $this->_resource;        
    }

    /**
     * Prepare text of item label
     */
    protected function _renderFromToItemLabel($from, $to)
    {
        $store      = Mage::app()->getStore();
        $fromPrice  = $store->formatPrice($from);
        $toPrice    = $store->formatPrice($to);
        return Mage::helper('catalog')->__('%s - %s', $fromPrice, $toPrice);
    }

    /**
     * Get data for build price filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        if (!Mage::getStoreConfig('amshopby/general/use_custom_ranges'))
            return parent::_getItemsData();
            
        $key = $this->_getCacheKey();

        $data = $this->getLayer()->getAggregator()->getCacheData($key);
        if ($data === null) {
            $ranges = $this->_getCustomRanges();
            $counts = $this->_getResource()->getFromToCount($this, $ranges);
            $data = array();
            
            foreach ($counts as $index => $count) {
                if (!$index) // index may be NULL if some products has price out of all ranges
                    continue;
                    
                $from  = $ranges[$index][0];
                $to    = $ranges[$index][1];
                $data[] = array(
                    'label' => $this->_renderFromToItemLabel($from, $to),
                    'value' => $from . ',' . $to,
                    'count' => $count,
                    'pos'   => $from,
                );
            }
            usort($data, array($this, '_srt')); 

            $tags = array(
                Mage_Catalog_Model_Product_Type_Price::CACHE_TAG,
            );
            $tags = $this->getLayer()->getStateTags($tags);
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }

    /**
     * Apply price range filter to collection
     *
     * @return Mage_Catalog_Model_Layer_Filter_Price
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filterBlock->setPriceFrom(Mage::helper('amshopby')->__('From'));
        $filterBlock->setPriceTo(Mage::helper('amshopby')->__('To'));
        
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter) {
            return $this;
        }

        $filter = explode(',', $filter);
        if (count($filter) != 2) {
            return $this;
        }

        list ($from, $to) = $filter;
        
        $isFromTo = false;
        if (Mage::getStoreConfig('amshopby/general/use_custom_ranges')){
            $isFromTo = true;
        }
            
        if (Mage::getStoreConfig('amshopby/general/price_from_to')) {
            if (strpos($from, '.') || strpos($to, '.')) {
                $isFromTo = true;
            }
        }        
        
        $from  = floatval($from);
        $to    = floatval($to);

        if ($from || $to) {
            if (!$isFromTo){
                $index = $from;
                $range = $to;
                $from  = ($index-1)*$range;
                $to    = $index*$range;
            }   
            
            $filterBlock->setPriceFrom($from > 0.01 ? $from : '');
            $filterBlock->setPriceTo($to > 0.01 ? $to : '');
            
            $this->_getResource()->applyFromToFilter($this, $from, $to);
            
            $this->getLayer()->getState()->addFilter(
                $this->_createItem($this->_renderFromToItemLabel($from, $to), $filter)
            );
        }
        return $this;
    }

    protected function _getCustomRanges()
    {
        //return array(24=>array(0,105), 25=>array(105,149), 35=>array(150,400));
        
        $ranges = array();
        $collection = Mage::getModel('amshopby/range')->getCollection()
            ->setOrder('price_frm','asc')
            ->load();
            
        $rate = Mage::app()->getStore()->getCurrentCurrencyRate(); 
        $prev = 0;   
        foreach ($collection as $range){
            $from = $range->getPriceFrm()*$rate;
//            if ($prev && ($from - $prev > 0.01)){
//                $from = $prev;
//            }            
            $prev = $to = $range->getPriceTo()*$rate;
            
            $ranges[$range->getId()] = array($from, $to);
        }
        
        if (!$ranges){
            echo "Please set up Custom Ranges in the Admin > Catalog > Improved Navigation > Ranges";
            exit;
        }
        
        return $ranges;
    }
    
    public function _srt($a, $b)
    {
        $res = ($a['pos'] < $b['pos']) ? -1 : 1;
        return $res;
    } 
    
    public function getMaxPriceInt()
    {
        if (!Mage::helper('amshopby')->isVersionLessThan(1, 4)){
            $maxPrice = $this->getData('max_price_int');
            if (is_null($maxPrice)) {
                $maxPrice = $this->_getResource()->getMaxPrice($this);
                $maxPrice = ceil($maxPrice);
                $this->setData('max_price_int', $maxPrice);
            }
            return $maxPrice;            
        }
        
        // I LOVE 1.3 :)
        $maxPrice = $this->getData('max_price_int');
        if (is_null($maxPrice)) {
            $maxPrice = $this->_getResource()->getMaxValue(
                $this->getAttributeModel(),
                $this->_getBaseCollectionSql()
            );

            $maxPrice = floor($maxPrice);
            $this->setData('max_price_int', $maxPrice);
        }
        return $maxPrice;        
    }

    /**
     * For 1.3 ONLY. I LOVE 1.3 :)
     */
    public function getRangeItemCounts($range)
    {
        //slider
        if (3 == Mage::getStoreConfig('amshopby/general/price_type')){
            return array(0=>1, 1=>2);
        }
        
        if (!Mage::helper('amshopby')->isVersionLessThan(1, 4)){
            return parent::getRangeItemCounts($range);
        }
        $items = $this->getData('range_item_counts_'.$range);
        if (is_null($items)) {
            $items = $this->_getResource()->getCount(
                $this->getAttributeModel(),
                $range,
                $this->_getBaseCollectionSql()
            );
            $this->setData('range_item_counts_'.$range, $items);
        }
        return $items;
    }
    
}
