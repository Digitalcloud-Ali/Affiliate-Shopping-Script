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

if (isset($_GET['KT_back'])) {
	require_once(dirname(realpath(__FILE__)). '/../common/KT_common.php');
	KT_session_start();
	
	$tmp = KT_addReplaceParam(KT_getFullUri(), 'KT_back');
	if (isset($_SERVER['HTTP_REFERER'])) {
		$backURL = $_SERVER['HTTP_REFERER'];
		$backURL = KT_addReplaceParam($backURL, '/^totalRows_.*$/i');
		KT_SessionKtBack($backURL);
	}

	if (isset($_POST['KT_Delete1'])) {
		echo '<html><head></head><body><form action="' . ($tmp) . '" method="POST" name="KT_backForm">';
		foreach($_POST as $key => $value) {
			if ($key == 'KT_Delete1' || strpos($key, 'kt_pk_') === 0) {
				if (get_magic_quotes_gpc()) {
					$value = stripslashes($value);
				}
				echo '<input type="hidden" name="' . $key . '" value="' . KT_escapeAttribute($value) . '" />';
			}
		}
		echo '</form><script>document.forms.KT_backForm.submit();</script></body></html>';
	} else {
		KT_redir($tmp);
	}
	exit;
}
?>