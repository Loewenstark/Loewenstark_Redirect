<?php

class Loewenstark_Redirect_Model_Observer {
    /*
     * Redirect unvisible configurable child URLs to parent with params
     */

    public function main() {

        $config = Mage::getStoreConfig('ls_redirect/general/redirect');

        if ($config) {
            $this->redirectToParent();
        } else {
            return false;
        }
    }

    public function redirectToParent() {
        $path_key = Mage::app()->getRequest()->getOriginalPathInfo();
        $path_key = ltrim($path_key, '/');

        $rewrite = Mage::getModel('core/url_rewrite')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->loadByRequestPath($path_key);
        $productId = $rewrite->getProductId();

        if (!$productId)
            return false;

        $child = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($productId);

        if ($child && $child->getId()) {
            $main_product_ids = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($child->getId());

            if (!$main_product_ids)
                return false;

            if ($main_product_ids && count($main_product_ids) > 0)
                $main_product_id = $main_product_ids[0];

            $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($main_product_id);
            $attributes = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);

            $params = array();
            foreach ($attributes as $attribute) {
                $attribute_id = $attribute['attribute_id'];
                $options_id = $child->getData($attribute['attribute_code']);
                $params[] = $attribute_id . '=' . $options_id;
            }

            $parameters = Mage::app()->getRequest()->getParams();

            $parameters_extended = Mage::helper('lsredirect')->buildHttpQuery($parameters);

            if($parameters_extended) {
                $url_queries = '?' . $parameters_extended;
            }

            $product_url = $product->getProductUrl();

            $result_url = $product_url . $url_queries . '#' . implode("&", $params);

            header("HTTP/1.1 301 Moved Permanently");
            header("location:" . $result_url);
            exit;
        } else {
            return false;
        }
    }

    public function addNoindexAndCanonicalForChilds(Varien_Event_Observer $observer)
    {
        $product  = Mage::registry('current_product');
        
        //only on simple products
        if($product->getTypeId() != "simple") return $this;
        
        //ony for products in search
        if($product->getVisibility()!=Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH) return $this;
        
        //search for parent product
        $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
        if(!$parentIds || !isset($parentIds[0])) return $this;

        //load parent
        $product_collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('entity_id', $parentIds[0])
            ->addUrlRewrite();
        $parent = $product_collection->getFirstItem();
        
        //check parent
        if(!$parent || !$parent->getId()) return $this;
                
        $value = $parent->getProductUrl();
        $robots = 'NOINDEX, FOLLOW';
        
        if (!empty($value))
        {
            $value = $this->cleanUrl($value);
            $link = '<'.$value.'>; rel="canonical"';
            
            Mage::app()->getResponse()->setHeader('Link', $link);
            Mage::app()->getResponse()->setHeader('X-Robots-Tag', $robots);

            $this->_getLayout()->getBlock('head')
                    ->removeItem('link_rel', $product->getProductUrl())
                    ->addLinkRel('canonical', $value)
                    ->setData('robots', $robots);
        }
    }
    
    /**
     *
     * @param string $url
     * @return string
     */
    public function cleanUrl($url)
    {
        return str_replace(array('?___SID=U', '&___SID=U'), '', $url);
    }
    
    /**
     *
     * @return Mage_Core_Model_Layout
     */
    protected function _getLayout()
    {
        return Mage::app()->getLayout();
    }

}
