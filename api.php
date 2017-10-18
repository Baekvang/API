<?php 
	var_dump($_REQUEST);
	var_dump($_SERVER);

	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/startloader.php');

	if(!empty($_REQUEST)) {
		$oAPI = new API($_REQUEST);
	} else { 
		die('Empty response given');
	}
?>