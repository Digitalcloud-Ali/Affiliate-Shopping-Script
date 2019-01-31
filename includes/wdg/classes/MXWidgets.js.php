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
	$d = 'WDG';

	KT_sendExpireHeader(60 * 60 * 24);
	header("Content-Type: application/JavaScript");
?>
//Javascript UniVAL Resources
WDG_Messages = {};
WDG_Messages['the_mask_is']           = '<?php echo KT_escapeJS(KT_getResource('the_mask_is', $d)); ?>';
WDG_Messages['the_format_is']         = '<?php echo KT_escapeJS(KT_getResource('the_format_is', $d)); ?>';
WDG_Messages['also_floats']           = '<?php echo KT_escapeJS(KT_getResource('also_floats', $d)); ?>';
WDG_Messages['also_negatives']        = '<?php echo KT_escapeJS(KT_getResource('also_negatives', $d)); ?>';
WDG_Messages['the_format_is']         = '<?php echo KT_escapeJS(KT_getResource('the_format_is', $d)); ?>';
WDG_Messages['max_character_number']  = '<?php echo KT_escapeJS(KT_getResource('max_character_number', $d)); ?>';
WDG_Messages['the_date_format_is']    = '<?php echo KT_escapeJS(KT_getResource('the_date_format_is', $d)); ?>';
WDG_Messages['calendar_button']       = '<?php echo KT_escapeJS(KT_getResource('calendar_button', $d)); ?>';
WDG_Messages['calendar_button']       = '<?php echo KT_escapeJS(KT_getResource('calendar_button', $d)); ?>';
WDG_Messages['rte_maximum_reached']   = '<?php echo KT_escapeJS(KT_getResource('rte_maximum_reached', $d)); ?>';
WDG_Messages['dyn_add_label_text']    = '<?php echo KT_escapeJS(KT_getResource('dyn_add_label_text', $d)); ?>';
WDG_Messages['dyn_are_you_sure_text'] = '<?php echo KT_escapeJS(KT_getResource('dyn_are_you_sure_text', $d)); ?>';
WDG_Messages['dyn_submit_text']       = '<?php echo KT_escapeJS(KT_getResource('dyn_submit_text', $d)); ?>';
WDG_Messages['dyn_default_option_text'] = '<?php echo KT_escapeJS(KT_getResource('dyn_default_option_text', $d)); ?>';