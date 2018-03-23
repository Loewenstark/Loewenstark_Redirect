<?php

class Loewenstark_Redirect_Helper_Data extends Mage_Core_Helper_Abstract
{
    /*
    *   Build url with parameters and remove id + status
    */

    function buildHttpQuery($query)
    {
        unset($query['id']);
        unset($query['__status__']);
        $query_array = array();
        foreach ($query as $key => $key_value) {
            $query_array[] = urlencode($key) . '=' . urlencode($key_value);
        }
        return implode('&', $query_array);
    }
}
