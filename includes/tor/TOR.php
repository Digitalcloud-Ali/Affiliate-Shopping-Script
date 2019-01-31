<?php
/*
 * ADOBE SYSTEMS INCORPORATED
 * Copyright 2007 Adobe Systems Incorporated
 * All Rights Reserved
 * 
 * NOTICE:  Adobe permits you to use, modify, and distribute this file in accordance with the 
 * terms of the Adobe license agreement accompanying it. If you have received this file from a 
 * source other than Adobe, then your use, modification, or distribution of it requires the prior 
 * written permission of Adobe.
 */

/*
	Copyright (c) InterAKT Online 2000-2006. All rights reserved.
*/

	$KT_TOR_uploadErrorMsg = '<strong>File not found:</strong> <br />%s<br /><strong>Please upload the includes/ folder to the testing server.</strong>';
	$KT_TOR_uploadFileList = array(
		'../common/KT_common.php',
		'../common/lib/db/KT_Db.php',
		'TOR_SetOrder.class.php');

	for ($KT_TOR_i=0;$KT_TOR_i<sizeof($KT_TOR_uploadFileList);$KT_TOR_i++) {
		$KT_TOR_uploadFileName = dirname(realpath(__FILE__)). '/' . $KT_TOR_uploadFileList[$KT_TOR_i];
		if (file_exists($KT_TOR_uploadFileName)) {
			require_once($KT_TOR_uploadFileName);
		} else {
			die(sprintf($KT_TOR_uploadErrorMsg,$KT_TOR_uploadFileList[$KT_TOR_i]));
		}
	}
	
	KT_setServerVariables();
	KT_session_start();
?>