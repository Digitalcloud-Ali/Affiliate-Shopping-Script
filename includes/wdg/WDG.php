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

	$KT_WDG_uploadErrorMsg = '<strong>File not found:</strong> <br />%s<br /><strong>Please upload the includes/ folder to the testing server.<strong>';
	$KT_WDG_uploadFileList = array('../common/KT_common.php', '../common/lib/db/KT_Db.php', 'WDG_JsRecordset.class.php', 'WDG_functions.inc.php');

	for ($KT_WDG_i=0;$KT_WDG_i<sizeof($KT_WDG_uploadFileList);$KT_WDG_i++) {
		$KT_WDG_uploadFileName = dirname(realpath(__FILE__)). '/' . $KT_WDG_uploadFileList[$KT_WDG_i];
		if (file_exists($KT_WDG_uploadFileName)) {
			require_once($KT_WDG_uploadFileName);
		} else {
			die(sprintf($KT_WDG_uploadErrorMsg,$KT_WDG_uploadFileList[$KT_WDG_i]));
		}
	}
KT_session_start();
?>