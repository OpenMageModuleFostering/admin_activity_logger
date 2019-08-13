<?php
/*
 * @package :   Ameex_Adminlog
 * @author  :   Ameex
 *
 */
class Ameex_Adminlog_Block_Adminhtml_GridContainer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('adminlog_grid');
		$this->setDefaultSort('logged_at');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}

    protected function _prepareCollection()
    {
		$collection=Mage::getModel('adminlog/adminlog')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();		
	}

	protected function _prepareColumns()
    {
        $this->addColumn('logged_at', array(
            'header'    => Mage::helper('adminlog')->__('Logged At'),
            'type'      => 'datetime',
            'index'     => 'logged_at',
            'gmtoffset' => true
        ));

        $this->addColumn('user_email', array(
            'header'    => Mage::helper('adminlog')->__('User Email'),
            'index'     => 'user_email'
        ));

        $this->addColumn('full_path', array(
            'header'    => Mage::helper('adminlog')->__('Full Path'),
            'index'     => 'full_path'
        ));

        $this->addColumn('controller_name', array(
            'header'    => Mage::helper('adminlog')->__('Viewed Section'),
            'index'     => 'controller_name'
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('adminlog')->__('Action'),
            'index'     => 'action'
        ));

        $this->addColumn('additional_info', array(
            'header'    => Mage::helper('adminlog')->__('Additional Information'),
            'index'     => 'additional_info'
        ));

        $this->addColumn('store_name', array(
            'header'    => Mage::helper('adminlog')->__('Store Name'),
            'index'     => 'store_name'
        ));

        $this->addColumn('remote_ip', array(
            'header'    => Mage::helper('adminlog')->__('IP Address'),
            'index'     => 'remote_ip'
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('adminlog')->__('CSV'));  
        $this->addExportType('*/*/exportXml', Mage::helper('adminlog')->__('XML'));  
        $this->addExportType('*/*/exportExcel', Mage::helper('adminlog')->__('EXCEL'));
 		
        return parent::_prepareColumns();
	}

	public function getGridUrl()
	{
	   return $this->getUrl('*/*/grid', array('_current'=>true));
	}

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');
        $this->getMassactionBlock()->setUseSelectAll(false);
        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('adminlog')->__('Delete Logs'),
             'url'  => $this->getUrl('*/adminlog/massDelete'),
        ));
        return $this;
    }
}
