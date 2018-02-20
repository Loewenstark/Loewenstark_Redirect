<?php

class Loewenstark_Redirect_Model_Observer
{
    /*
     * Redirect unvisible configurable child URLs to parent with params
     */
    public function main()
    {

        $config = Mage::getStoreConfig('ls_redirect/general/redirect');

        if ($config)
        {
            $this->redirectToParent();
        } else
        {
            return false;
        }
    }

    public function redirectToParent()
    {
        $path_key = Mage::app()->getRequest()->getOriginalPathInfo();
        $path_key = ltrim($path_key,'/');

        $rewrite = Mage::getModel('core/url_rewrite')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->loadByRequestPath($path_key);
        $productId = $rewrite->getProductId();
                
        if(!$productId)
            return false;
        
        $child = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($productId);

        if ($child && $child->getId())
        {            
            $main_product_ids = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($child->getId());
            if($main_product_ids && count($main_product_ids) > 0)
                $main_product_id = $main_product_ids[0];
            
            $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($main_product_id);
            $attributes = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
            $params = array();
            foreach ($attributes as $attribute)
            {
                $attribute_id = $attribute['attribute_id'];
                $options_id = $child->getData($attribute['attribute_code']);
                $params[] = $attribute_id . '=' . $options_id;
            }
            $product_url = $product->getProductUrl();
            $result_url = $product_url . '#' . implode("&", $params);
            header("HTTP/1.1 301 Moved Permanently");
            header("location:" . $result_url);
            exit;
        } else
        {
            return false;
        }
    }

}
