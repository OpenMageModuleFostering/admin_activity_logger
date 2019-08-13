<?php
/*
 * @package :   Ameex_Adminlog
 * @author  :   Ameex
 *
 */
class Ameex_Adminlog_Model_Resource_Adminlog_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {  
        $this->_init('adminlog/adminlog');
    } 
}
