<?php

class Cmsmart_Onlinedesign_Model_Mysql4_Font extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the productdesign_id refers to the key field in your database table.
        $this->_init('onlinedesign/font', 'font_id');
    }
}