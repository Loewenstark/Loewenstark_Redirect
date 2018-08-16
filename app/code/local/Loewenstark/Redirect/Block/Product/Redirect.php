<?php

class Loewenstark_Redirect_Block_Product_Redirect extends Mage_Core_Block_Template
{
    public function canShow()
    {
        return !$this->_bot_detected() && $this->getProduct() && $this->getProduct()->getId() && $this->getProduct()->getVisibility()==3;
    }

    public function _bot_detected()
    {
        return (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT']));
    }

    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    public function getParentUrl()
    {
        $child = $this->getProduct();
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

        return $result_url;
    }
}
