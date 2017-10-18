<?php 
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/startloader.php');

	if(!empty($_REQUEST)) {
		$oAPI = new Customer($_REQUEST);
	} else { 
		die('Empty response given');
	}
?>