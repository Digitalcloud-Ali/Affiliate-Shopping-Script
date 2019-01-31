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

if (array_search('all', $GLOBALS['nav_arrCategory'])) {
	$all_index = array_search('all', $GLOBALS['nav_arrCategory']);
	unset($GLOBALS['nav_arrCategory'][$all_index]);
}
$NAV_DEP_PN_total = count($GLOBALS['nav_arrCategory']);
$NAV_DEP_PN_arr = $GLOBALS['nav_arrCategory'];
$NAV_DEP_PN_i = 0;
$NAV_DEP_PN_exit = 0;

foreach ($NAV_DEP_PN_arr as $NAV_DEP_PN_val) {
	if (!isset($NAV_DEP_PN_first)) {
		$NAV_DEP_PN_first = $NAV_DEP_PN_val;
	}
	if ($NAV_DEP_PN_val == $GLOBALS['nav_selected']) {
		$NAV_DEP_PN_exit = 1;
	} else if ($NAV_DEP_PN_exit==0) {
		$NAV_DEP_PN_prev = $NAV_DEP_PN_val;
	}
	if ($NAV_DEP_PN_exit==1 && $NAV_DEP_PN_val != $GLOBALS['nav_selected']) {
		$NAV_DEP_PN_next = $NAV_DEP_PN_val;
		$NAV_DEP_PN_exit = 2;
	}
	$NAV_DEP_PN_last = $NAV_DEP_PN_val;
	$NAV_DEP_PN_i++;
}
?>

<div class="KT_textnav clearfix">
  <ul>
		<li class="first">
			<a href="<?php 
				if (isset($NAV_DEP_PN_prev)) {
					echo $GLOBALS['nav_currentPage'] . $GLOBALS['nav_queryString'] . urlencode($NAV_DEP_PN_first);
				} else {
					echo "javascript: void(0);";
				}?>"><?php echo NAV_getResource("First"); ?></a>
		</li>
		<li class="prev">
				<a href="<?php
				if (isset($NAV_DEP_PN_prev)) {
					echo $GLOBALS['nav_currentPage'] . $GLOBALS['nav_queryString'] . urlencode($NAV_DEP_PN_prev);
				} else {
					echo "javascript: void(0);";
				}
				?>"><?php echo NAV_getResource("Previous"); ?></a>
		</li>

		<li>
		<?php
		if ($GLOBALS['nav_selected']=='' || trim($GLOBALS['nav_selected'])=='') {
			$NAV_DEP_PN_dVal = '**';
		} else {
			$NAV_DEP_PN_dVal = $GLOBALS['nav_selected'];
		}
		echo $NAV_DEP_PN_dVal;
		?>
		</li>
		
		<li class="next">
			<a href="<?php 
				if (isset($NAV_DEP_PN_next)) {
					echo $GLOBALS['nav_currentPage'] . $GLOBALS['nav_queryString'] . urlencode($NAV_DEP_PN_next);
				} else {
					echo "javascript: void(0);";
				}?>"><?php echo NAV_getResource("Next"); ?></a>
		</li>
		<li class="last">
			<a href="<?php
				if (isset($NAV_DEP_PN_next)) {
					echo $GLOBALS['nav_currentPage'] . $GLOBALS['nav_queryString'] . urlencode($NAV_DEP_PN_last);
				} else {
					echo "javascript: void(0);";
				}?>"><?php echo NAV_getResource("Last"); ?></a>
		</li>
  </ul>
</div>