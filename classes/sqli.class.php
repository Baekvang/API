<?php
	date_default_timezone_set('Europe/Copenhagen');

	class SQLi {
		var $timeNow = "2016.01.01-00:00:01";
		var $sDatabaseName;

		public function __construct($sDatabase = NULL) {
		    $this->timeNow = date('Y.m.d-H:i:s',time());
		    $this->sDatabaseName = $sDatabase;

		    // This setup allows multiple databses to be added via a name. (read: /includes/config.php)
			switch($sDatabase) {
				case DB_NAME_1:
					$this->hConnectionID = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die($this->die_error());	
					mysqli_select_db($this->hConnectionID, DB_NAME_1) or $this->_error(mysqli_error($this->hConnectionID));
					break;	
			}
			$this->hConnectionID->set_charset("utf8mb4");
		}

		public function die_error() {
			echo 'Something went wrong, with setting up a database connection';
			return;
		}

		function _log($sQuery) {
			$fp = fopen($_SERVER['DOCUMENT_ROOT']."/SQL_ERROR_LOG.TXT", "a");
			fwrite($fp, "\n*************************************************************************************************\n");
			fwrite($fp, $sQuery);
			fclose($fp);
			return;
		}

		function _error($sText) {
			$sText .= "<br><br>".$_SERVER['SCRIPT_URI'];
			$sText .= "<br />" . $_ENV['HTTP_REFERER']."?".$_ENV['REDIRECT_QUERY_STRING'];
			$sText .= "<br />IP: " . $_SERVER["REMOTE_ADDR"];

			$this->_log($sText);

			die("\nFAILED:\n\n$sText\n");
			return;
		}

	    public function prepareStatement($sQuery, $aParams = array(), $bReturnInsertedID = false) {
	    	$oStmt = mysqli_prepare($this->hConnectionID, $sQuery);

	    	if($oStmt === false) {
	    		$this->_error($this->timeNow . '<br />The query was incorrect and could not be prepared by mysqli!<br />' . $sQuery . '<br />' . mysqli_error($this->hConnectionID));
	    		return false;
	    	}

	    	$aBindParams = array(0 => '');
	    	$aParamRefs = array();
	    	$aReturnArray = array();
	    	if(count($aParams) > 0) {
		    	foreach($aParams as $sParam) { 
		    		$aBindParams[0] .= 's';
		    		if(!is_null($sParam) && !is_numeric($sParam) && !is_bool($sParam)) {
		    			$sParam = (string) trim($sParam);
		    			if ($sParam === 'NULL') $sParam = NULL;
		    		} 
		    		array_push($aBindParams, $sParam);
		    	}
		    	$iCountParams = count($aBindParams);
		    	for($i = 0; $i < $iCountParams; $i++) {
		    		$aParamRefs[] = & $aBindParams[$i];
		    	}

		    	if(!call_user_func_array(array($oStmt, 'bind_param'), $aParamRefs)) {
		    		$this->_error($this->timeNow . "<br />The params could not be bound to the prepared query!\n" . implode(',', $aParams) . "\n" . $sQuery);
		    		return false;
		    	}
		    }

	    	if($oStmt->execute()) {
	    		if($bReturnInsertedID) {
	    			return $this->getID();
	    		} else {
		    		$oResult = mysqli_stmt_get_result($oStmt);
		    		if(is_bool($oResult)) return true;
		    		
		    		while($aData = mysqli_fetch_array($oResult, MYSQLI_ASSOC)) {
		    			$aReturnArray[] = $aData;
		    		}
		    		return $aReturnArray;
		    	}
	    	} else {
	    		$this->_error($this->timeNow . " " . $sQuery . PHP_EOL . json_encode($aParams) . PHP_EOL . $sQuery . PHP_EOL. mysqli_error($this->hConnectionID) . PHP_EOL);

	    		return false;
	    	}
	    }
	}
?>