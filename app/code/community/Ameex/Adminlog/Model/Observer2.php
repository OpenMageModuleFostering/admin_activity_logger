<?php

class Ameex_Adminlog_Model_Observer
{
	public function getLog()
	{   if(!isset($_COOKIE['path'])) {
        setcookie($_COOKIE['path'],' ');;
        }
	    $isActive = Mage::getStoreConfig('adminlog_options/adminlog_group/adminlog_enable');
        $request = Mage::app()->getRequest();
        //echo "<pre>";
        // print_r($request->getRequestedRouteName());
        // print_r(get_class_methods($request));
        $currentPath = $request->getOriginalPathInfo(); 
        
	    if(($isActive == 1) && ($currentPath != $_COOKIE['path'])) {
	       	$openActions = array('Index','Validate','Edit','Grid');
			$request = Mage::app()->getRequest();
            $fullPathInfo = $request->getOriginalPathInfo();
            setcookie($_COOKIE['path'],$fullPathInfo);
            $controllerName = $request->getControllerName();
            $newActionControllerName = explode('_',$controllerName);
            $newActionControllerName = end($newActionControllerName);
		    $implodedControllerName = implode(' => ', array_map('ucfirst', explode('_', $controllerName)));
			$actionName = ucwords($request->getActionName());
			$adminlog = Mage::getModel('adminlog/adminlog');
			$adminLogLastItem = $adminlog->getCollection()->getLastItem();
			$lastItemControllerName = $adminLogLastItem->getControllerPath();
			$lastItemActionName = $adminLogLastItem->getActionPath();
			$lastItemSectionId = $adminLogLastItem->getSectionId();
			$id = $this->getDynamicVal($controllerName);
			$activeId = $request->getParam($id[0]);
			$adminlog->setSectionId($activeId);
			if((!in_array($actionName,$openActions) && ($implodedControllerName != 'Adminlog'))) {
		    	$moduleName = $request->getModulename(); 
		    	$currentPath = $request->getOriginalPathInfo();

		    	$storeId =  $request->getParam('store');
				$store = Mage::getModel('core/store')->load($storeId);
				$storeName = $store->getName();

				$adminSession = Mage::getSingleton('admin/session');
				$user = $adminSession->getUser();
				if ($user) {
					$userId = $user->getUserId();
					$userName = $user->getName();
					$userEmail = $user->getEmail();
					$remoteIp = Mage::helper('core/http')->getRemoteAddr();
					
					$adminlog->setCustomerId($userId);
					$adminlog->setCustomerEmail($userEmail);
					$adminlog->setAdminFrontname($moduleName);
					if($storeName) {
						$adminlog->setStoreName($storeName);
				    } else {
				    	$adminlog->setStoreName("All Store Views");
				    }
					$adminlog->setControllerPath(ucfirst($implodedControllerName));
					$adminlog->setActionPath(ucfirst($actionName));
					
					
					if(!empty($id)) {
						
			    		if(empty($id[1])) {
			    			$activeName = $activeId." ".ucwords($id[0]);
			    		} else {
			    			$activeName = Mage::getModel($id[1])->load($activeId)->getData($id[2]);
			    		}
			    		if (($actionName == "Save") && ($id[1] == "catalog/product")) { 
							$activeName= $request->getParam('product');
							$adminlog->setAdditionalInfo($id[3]." ".$activeName['sku']);
			    		} else if (($actionName == "Save") && ($id[1] == "customer/customer")) {
							$activeName = $request->getParam('account');
							$adminlog->setAdditionalInfo($id[3]." ".$activeName['firstname']." ".$activeName['lastname']);
			    		} else if($actionName == "Save") {
			    			$activeName = $id[3]." ".$request->getParam($id[4]);
			    		    $adminlog->setAdditionalInfo($activeName);
			    		} else if (($actionName == "New")) {
			    		 	$adminlog->setAdditionalInfo("Trying to create a new ".$newActionControllerName);
			    		}
			    		else if (($actionName == "Duplicate")) {

			    		 	$adminlog->setAdditionalInfo("Duplicated the ".$newActionControllerName);
			    		}
			    		else if (($actionName == "Delete")) {

			    		 	$adminlog->setAdditionalInfo("Deleted the ".$newActionControllerName);
			    		} 
			    	} else {
			    		$adminlog->setAdditionalInfo("Modified Content is:"." ".str_replace("=>", " ",$implodedControllerName)." Section");
			    	}
                    $urlPath = $moduleName."/".$controllerName."/".lcfirst($actionName);
                   	$adminlog->setViewPath($urlPath);
		    		$adminlog->setViewedAt(time());
					$adminlog->setRemoteIp($remoteIp);
					
						$adminlog->save();
				}
			}
		}	
	}
	

	public function getDynamicVal($controllerName)
	{
		switch ($controllerName) {
			case 'customer':
				return array('id', 'customer/customer', 'name','Saved Customer Name Is :','firstname');
				break;
			case 'catalog_product':
				return array('id','catalog/product', 'sku','Saved Product Sku Is :','sku');
				break;
			case 'cms_page':
				return array('page_id','cms/page','title','Saved Cms Page Title Is :','title');
				break;
			case 'cms_block':
				return array('block_id','cms/block','title','Saved Cms Block Title Is :','title');
				break;
			case 'system_config':
				return array('section','','','Saved Section Is :','section');
				break;
			default:
				return '';
				break;
		}
	}

    public function cleanLog($observer)
    {
     	$isExpire = Mage::getStoreConfig('adminlog_options/adminlog_group/adminlog_expire');
		$time = time();
		$to = date('Y-m-d H:i:s', $time);
		$logs = Mage::getResourceModel('adminlog/adminlog_collection')
				    ->addFieldToSelect('id')
				    ->addFieldToSelect('viewed_at');
	    foreach ($logs as $log) {
	    		$time1 = new DateTime($log->getViewedAt());
				$time2 = new DateTime($to);
				$interval = $time1->diff($time2);
				$daysExpire = $interval->d;
				if($daysExpire > $isExpire){
					$log->delete();
				}
	    }
    }

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