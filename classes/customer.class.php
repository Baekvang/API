<?php
	class Customer extends API { 
		function createCustomer($aData) {
			if(empty($aData['email']) || empty($aData['password'])) return false;

			$oSQL = new SQLi(DB_NAME_1);
			$sQuery = "
				INSERT INTO " . TABLE_CUSTOMERS . " (email, password) VALUES (?, ?)
			";

			$oSQL->prepareStatement($sQuery, array($aData['email'], $aData['password']));
		}
	}
?>