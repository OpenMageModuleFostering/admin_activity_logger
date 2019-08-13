<?php
/*
 * @package :   Ameex_Adminlog
 * @author  :   Ameex
 *
 */
class Ameex_Adminlog_Model_Resource_Adminlog extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		 $this->_init('adminlog/adminlog','id');
	}
}
