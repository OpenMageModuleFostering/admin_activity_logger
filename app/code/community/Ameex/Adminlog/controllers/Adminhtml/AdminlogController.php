<?php 
/*
 * @package :   Ameex_Adminlog
 * @author  :   Ameex
 *
 */
class Ameex_Adminlog_Adminhtml_AdminlogController extends Mage_Adminhtml_Controller_Action
{

    public $_currentDate;

    public function  _construct()
    {
        parent::_construct();
        $this->_currentDate = date("d-m-Y", Mage::getModel('core/date')->timestamp(time()));
    }
	public function _initAction()
	{
        $this->loadLayout()->_setActiveMenu('adminlogdetails/adminlog')->_addBreadcrumb(Mage::helper('adminlog')->__('Admin Log Activity Manager'), Mage::helper('adminlog')->__('Admin Log Activity Manager'));
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('adminlog/adminhtml_gridContainer'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminlog/adminhtml_gridContainer_grid')->toHtml());
    }

    public function exportCsvAction()  
    {  
        $fileName   = 'adminlog.csv'."_".$this->_currentDate;  
        $content    = $this->getLayout()->createBlock('adminlog/adminhtml_gridContainer_grid');  
        $this->_prepareDownloadResponse($fileName, $content->getCsvFile());  
    }

    public function exportXmlAction()  
    {  
        $fileName   = 'adminlog.xml'."_".$this->_currentDate;  
        $content    = $this->getLayout()->createBlock('adminlog/adminhtml_gridContainer_grid');
        $this->_prepareDownloadResponse($fileName, $content->getXml());  
    }

    public function exportExcelAction()  
    {  
        $fileName   = 'adminlog.xls'."_".$this->_currentDate;  
        $content    = $this->getLayout()->createBlock('adminlog/adminhtml_gridContainer_grid');  
        $this->_prepareDownloadResponse($fileName, $content->getExcelFile());  
    }

    public function massDeleteAction()  
    {  
        $logIds = $this->getRequest()->getPost('id');
        try {
            $adminlog = Mage::getSingleton('adminlog/adminlog');
            foreach ($logIds as $logId) {
                $adminlog->load($logId)->delete();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Total of %d record(s) were deleted.', count($logIds)));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
    
}
