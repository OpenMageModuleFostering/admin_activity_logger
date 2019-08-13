<?php
class Ameex_Adminlog_Model_Observer
{
	public function modelSaveAfter($observer)
    {
      
    	$isActive = Mage::getStoreConfig('adminlog_options/adminlog_group/adminlog_enable');
        if($isActive == 1) {
            $adminSession = Mage::getSingleton('admin/session');
            $user = $adminSession->getUser();
            $openControllerNames = array("Index => Event","CatalogInventory => Stock");
            $requestModuleName = Mage::app()->getRequest()->getModulename();
            $request = $observer->getEvent()->getObject();
            echo $requestModelName = get_class($request);
            echo "<pre>";
            //  print_r($request->getData());
            if($requestModelName == "Ameex_Adminlog_Model_Adminlog"){
                return;
            }
            $implodedRequestModelName = explode('_', $requestModelName);
            $controllerName = $implodedRequestModelName[1]." => ".$implodedRequestModelName[3];
            $actionName = "Save";  
            if(($user) && (!in_array($controllerName,$openControllerNames)))  {
                $storeId = $request->getStoreId();
                $store = Mage::getModel('core/store')->load($storeId);
                $storeName = $store->getName();
                $userId = $user->getUserId();
                $userEmail = $user->getEmail();
                $remoteIp = Mage::helper('core/http')->getRemoteAddr();
                $adminlog = Mage::getModel('adminlog/adminlog');
                $adminlog->setCustomerId($userId);
                $adminlog->setCustomerEmail($userEmail);
                $adminlog->setAdminFrontname($requestModuleName);
                $adminlog->setControllerPath(ucfirst($controllerName));
                $adminlog->setActionPath($actionName);
                $adminlog->setAdditionalInfo(1);
                $adminlog->setSectionId(1); 
                if($storeName){
                    $adminlog->setStoreName($storeName);
                } else {
                    $adminlog->setStoreName("All Store Views");
                }
                $adminlog->setViewedAt(time());
                $adminlog->setRemoteIp($remoteIp);  
                $adminlog->save();    
            }
        }  
    }       
}