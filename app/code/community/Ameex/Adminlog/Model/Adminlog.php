<?php
/*
 * @package :   Ameex_Adminlog
 * @author  :   Ameex
 *
 */
class Ameex_Adminlog_Model_Adminlog extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		parent::_construct();
		$this->_init('adminlog/adminlog');
	}
}
