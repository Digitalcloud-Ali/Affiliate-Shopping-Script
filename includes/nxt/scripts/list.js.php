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

	include_once(dirname(realpath(__FILE__)) . '/../../common/lib/resources/KT_Resources.php');
	$d = 'NXT';

	KT_sendExpireHeader(60 * 60 * 24);
	header("Content-Type: application/JavaScript");
?>
//Javascript UniVAL Resources
if (typeof(NXT_Messages) == 'undefined') {
	NXT_Messages = {};
}
NXT_Messages['are_you_sure_move']   = '<?php echo KT_escapeJS(KT_getResource('ARE_YOU_SURE_MOVE', $d)); ?>';
NXT_Messages['are_you_sure_delete'] = '<?php echo KT_escapeJS(KT_getResource('ARE_YOU_SURE_DELETE', $d)); ?>';
NXT_Messages['please_select_record'] = '<?php echo KT_escapeJS(KT_getResource('PLEASE_SELECT_RECORD', $d)); ?>';