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

    public function redirectToParent($observer) {

        $request_url = Mage::helper('core/url')->getCurrentUrl();
        $path_key = end(split('/', $request_url));


        $products = Mage::getModel('catalog/product')->getCollection()
                        ->addAttributeToSelect(array('url_path'))
                        ->addAttributeToFilter('url_path', array('eq' => $path_key))
                        ->setCurPage(1)->setPageSize(1);

        if ($products->count() >= 1) {
            $product_child = $products->getFirstItem();
            $child_id = $product_child->getId();
            $child = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($child_id);
            $main_product_id = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($child_id);
            $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($main_product_id);
            $attributes = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
            $params = array();
            foreach ($attributes as $attribute) {
                $attribute_id = $attribute['attribute_id'];
                $options_id = $child->getData($attribute['attribute_code']);
                $params[] = $attribute_id . '=' . $options_id;
            }
            $product_url = $product->getProductUrl();
            $result_url = $product_url . '#' . implode("&", $params);
            header("HTTP/1.1 301 Moved Permanently");
            header("location:" . $result_url);
            exit;
        } else {
            return false;
        }
    }

}
