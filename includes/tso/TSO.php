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

	$KT_TSO_uploadErrorMsg = '<strong>File not found:</strong> <br />%s<br /><strong>Please upload the includes/ folder to the testing server.</strong>';
	$KT_TSO_uploadFileList = array(
		'../common/KT_common.php',
		'TSO_TableSorter.class.php');

	for ($KT_TSO_i=0;$KT_TSO_i<sizeof($KT_TSO_uploadFileList);$KT_TSO_i++) {
		$KT_TSO_uploadFileName = dirname(realpath(__FILE__)). '/' . $KT_TSO_uploadFileList[$KT_TSO_i];
		if (file_exists($KT_TSO_uploadFileName)) {
			require_once($KT_TSO_uploadFileName);
		} else {
			die(sprintf($KT_TSO_uploadErrorMsg,$KT_TSO_uploadFileList[$KT_TSO_i]));
		}
	}
	
	KT_setServerVariables();
	KT_session_start();
?>