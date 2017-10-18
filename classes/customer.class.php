<?php
	class Customer extends API { 
		public function createCustomer($aData) {
			if(empty($aData['email']) || empty($aData['password'])) {
				return array('success' => 0, 'response' => 'There are data missing');
			}

			$oSQL = new SQLi(DB_NAME_1);
			$sQuery = "
				INSERT INTO " . TABLE_CUSTOMERS . " (email, password) VALUES (?, ?)
			";

			if($oSQL->prepareStatement($sQuery, array($aData['email'], $aData['password']))) {
				return array('success' => 1, 'response' => 'The customer was created correctly');
			} else {
				return array('success' => 0, 'response' => 'Something went wrong with creating the customer');
			}
		}

		public function changePassword($aData) {
			if(empty($aData['id']) || empty($aData['old-password']) || empty($aData['password']) || empty($aData['password-repeat'])) {
				return array('success' => 0, 'response' => 'There are data missing');
			}

			$oSQL = new SQLi(DB_NAME_1);
			$sQuery = "
				INSERT INTO " . TABLE_CUSTOMERS . " (id, password) VALUES (?, ?) ON DUPLICATE KEY UPDATE password = VALUES(password)
			";

			if(!$oSQL->prepareStatement($sQuery, array($aData['id'], $aData['password']))) {
				return array('success' => 1, 'response' => 'Password has been changed correctly');
			} else {
				return array('success' => 0, 'response' => 'Something went wrong when changing the password');
			}
		}
	}
?>