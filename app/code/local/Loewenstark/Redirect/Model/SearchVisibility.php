<?php

class Loewenstark_Redirect_Model_SearchVisibility extends Mage_Catalog_Model_Product_Visibility {

    /**
     * Add visibility in searchfilter to collection
     *
     * @deprecated
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @return Mage_Catalog_Model_Product_Visibility
     */
    public function getVisibleInSearchIds() {
        return array(self::VISIBILITY_BOTH);
    }

}

