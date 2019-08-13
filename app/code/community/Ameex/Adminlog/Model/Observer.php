<?php
/*
 * @package :   Ameex_Adminlog
 * @author  :   Ameex
 *
 */

class Ameex_Adminlog_Model_Observer
{

	public function getLog()
	{
		/*get the current path details */
		
        $request = Mage::app()->getRequest();
		$actionName = $request->getActionName();
        $controllerName = $request->getControllerName();
        $implodedControllerName = implode(' => ', array_map('ucfirst', explode('_', $controllerName)));

	    if ($this->_isLogNeeded($actionName, $implodedControllerName)) {

            $entity = end(explode('_', $controllerName));
            $user = Mage::getSingleton('admin/session')->getUser();

			$adminlog = Mage::getModel('adminlog/adminlog');
			$dynamicValues = $this->_getDynamicValues($controllerName);
			$currentId = $request->getParam($dynamicValues[0]);
	    	$storeId =  $request->getParam('store');
			$store = Mage::getModel('core/store')->load($storeId);

			if ($user) {
                /* Get the user details */
                  
				$data['user_id'] = $user->getUserId();
				$data['user_email'] = $user->getEmail();
				$data['remote_ip'] = Mage::helper('core/http')->getRemoteAddr();
				$data['store_name'] = $store->getName() ? $store->getName() : 'All store views';
				$data['controller_name'] = $implodedControllerName;
				$data['action'] = ucfirst($actionName);
				$data['full_path'] = $request->getModulename() . "/" . $controllerName . "/" . $actionName;
				$data['logged_at'] = time();

				$data['additional_info'] = "";
				if (!empty($dynamicValues)) {
		    		if (($actionName == "save") && ($dynamicValues[1] == "catalog/product")) {
		    			$product = $request->getParam('product');
						$data['additional_info'] = $dynamicValues[3] . $product['sku'];
		    		} elseif (($actionName == "save") && ($dynamicValues[1] == "customer/customer")) {
						$account = $request->getParam('account');
						$data['additional_info'] = $dynamicValues[3] . $account['firstname'] . " " . $account['lastname'];
		    		} elseif ($actionName == "save") {
		    			$data['additional_info'] = $dynamicValues[3] . $request->getParam($dynamicValues[4]);
		    		} elseif (($actionName == "new")) {
		    		 	$data['additional_info'] = "Tried to create a new " . $entity;
		    		} elseif (($actionName == "duplicate")) {
		    			$productId = $request->getParam('id');
		    			$_product = Mage::getModel('catalog/product')->load($productId);  
						$data['additional_info'] = "Duplicated the " . $entity." ".$_product['sku'];
		    		} elseif (($actionName == "delete")) {
		    		 	$data['additional_info'] = "Deleted the " . $entity;
		    		}

		    	} else {
		    		$data['additional_info'] = str_replace(" => ", " ", $implodedControllerName) . " section was modified.";
		    	}

                try {
                	$adminlog->setData($data)->save();
                } catch (Exception $e) {
                	Mage::getSingleton('core/session')->addError($entity . ' saved successfully. However, there occured an error while saving the log.');
                }
			}
		}
	}


	protected function _getDynamicValues($controllerName)
	{
		switch ($controllerName) {
			case 'customer':
				return array('id', 'customer/customer', 'name', 'Name of the saved customer : ', 'firstname');
			case 'catalog_product':
				return array('id', 'catalog/product', 'sku', 'SKU of the saved product : ', 'sku');
			case 'cms_page':
				return array('page_id', 'cms/page', 'title', 'Title of the saved CMS page : ', 'title');
			case 'cms_block':
				return array('block_id', 'cms/block', 'title', 'Title of the saved static block : ', 'title');
			case 'system_config':
				return array('section', '', '', 'Saved section : ', 'section');
			default:
				return '';
		}
	}

    public function cleanLog($observer)
    {
		/* log maintain details */
		
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
			if($daysExpire > $isExpire) {
				$log->delete();
			}
	    }
    }

    protected function _isLogNeeded($actionName, $implodedControllerName)
    {
		/*Filter the log actions */
		
    	$isActive = Mage::getStoreConfig('adminlog_options/adminlog_group/adminlog_enable');
    	$excludedActions = array('index','validate','edit','grid');
    	return (($isActive == 1) && !in_array($actionName, $excludedActions) && ($implodedControllerName != 'Adminlog'));
    }

}
