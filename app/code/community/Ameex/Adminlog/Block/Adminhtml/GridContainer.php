<?php
/*
 * @package :   Ameex_Adminlog
 * @author  :   Ameex
 *
 */
class Ameex_Adminlog_Block_Adminhtml_GridContainer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller='adminhtml_gridContainer';
		$this->_blockGroup='adminlog';
		$this->_headerText=Mage::helper('adminlog')->__('Admin activity log manager');
		parent::__construct();
		$this->_removeButton('add');		
	}
}
