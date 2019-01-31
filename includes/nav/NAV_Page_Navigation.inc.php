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

$NAV_PAGE_k = 0;
$NAV_PAGE_max = $nav_totalPages;

$NAV_PAGE_diff_left = floor(($GLOBALS['nav_noPagesToDisplay'] - 1) / 2);
$NAV_PAGE_diff_right = $GLOBALS['nav_noPagesToDisplay'] - $NAV_PAGE_diff_left - 1;
$NAV_PAGE_k = $nav_pageNum - $NAV_PAGE_diff_left;
$NAV_PAGE_max = $nav_pageNum + $NAV_PAGE_diff_right;

if ($NAV_PAGE_k < 0) {
	$NAV_PAGE_max = $NAV_PAGE_max - $NAV_PAGE_k;
	$NAV_PAGE_k = 0;
}
if ($NAV_PAGE_max > $nav_totalPages) {
	$NAV_PAGE_k = $NAV_PAGE_k - ($NAV_PAGE_max - $nav_totalPages);
	$NAV_PAGE_max = $nav_totalPages;
	if ($NAV_PAGE_k < 0) {
		$NAV_PAGE_k = 0;
	}
}
?>

<div class="KT_textnav clearfix">
  <ul>
		<li class="first">
			<a href="<?php 
				if ($nav_pageNum > 0) {
					printf("%s?pageNum_".$nav_rsName."=%d&totalRows_".$nav_rsName."=%d%s", $nav_currentPage, 0, $nav_totalRows, $nav_queryString); 
				} else {
					echo "javascript: void(0);";
				}?>"><?php echo NAV_getResource("First"); ?></a>
		</li>
		<li class="prev">
				<a href="<?php
				if ($nav_pageNum > 0) {
					printf("%s?pageNum_".$nav_rsName."=%d&totalRows_".$nav_rsName."=%d%s", $nav_currentPage, max(0, $nav_pageNum - 1), $nav_totalRows, $nav_queryString);
				} else {
					echo "javascript: void(0);";
				}
				?>"><?php echo NAV_getResource("Previous"); ?></a>
		</li>
		
		<?php
		for ($NAV_PAGE_i=$NAV_PAGE_k; $NAV_PAGE_i<=$NAV_PAGE_max; $NAV_PAGE_i++) {
			if ($NAV_PAGE_i==$nav_pageNum) {
		?>
			<li class="NAV_selected">
				<?php echo ($NAV_PAGE_i+1); ?>
			</li>
		<?php
			} else {
		 ?>
		 	<li>
				<a href="<?php printf("%s?pageNum_".$nav_rsName."=%d%s", $nav_currentPage, $NAV_PAGE_i, $nav_queryString); ?>"><?php echo ($NAV_PAGE_i+1) ; ?></a>
			</li>
		<?php
			}
		}
		?>
		
		<li class="next">
			<a href="<?php 
				if ($nav_pageNum < $nav_totalPages) {
					printf("%s?pageNum_".$nav_rsName."=%d&totalRows_".$nav_rsName."=%d%s", $nav_currentPage, min($nav_totalPages, $nav_pageNum + 1), $nav_totalRows, $nav_queryString); 
				} else {
					echo "javascript: void(0);";
				}?>"><?php echo NAV_getResource("Next"); ?></a>
		</li>
		<li class="last">
			<a href="<?php
				if ($nav_pageNum < $nav_totalPages) {
					printf("%s?pageNum_".$nav_rsName."=%d&totalRows_".$nav_rsName."=%d%s", $nav_currentPage, $nav_totalPages, $nav_totalRows, $nav_queryString); 
				} else {
					echo "javascript: void(0);";
				}?>"><?php echo NAV_getResource("Last"); ?></a>
		</li>
  </ul>
</div>