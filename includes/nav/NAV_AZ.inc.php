<div class="KT_textnav KT_textnav_AZ clearfix">
  <ul>
<?php
if ($GLOBALS['nav_linkRenderType']==1) {
	// only with records
	$NAV_AZ_arr = $GLOBALS['nav_arrLetters'];
} else {
	// all letters with or without link;
	$NAV_AZ_arr = range('A','Z');
	if ($GLOBALS['nav_useNumbers']  === true) {
		$NAV_AZ_arr[] = "0_9";
	}
	$NAV_AZ_arr[] = "other";
	$NAV_AZ_arr[] = "all";
}
$NAV_AZ_curIndex = 0;
foreach ($NAV_AZ_arr as $NAV_AZ_val) {
	$NAV_AZ_curIndex ++;
	$show_pipe = true;
	if ($NAV_AZ_curIndex == count($NAV_AZ_arr)) {
		$show_pipe = false;
	}
	
	if ($NAV_AZ_val === '') {
		$NAV_AZ_dVal = '**';
	} else {
		$NAV_AZ_dVal = $NAV_AZ_val;
	}
	if ($NAV_AZ_val === $GLOBALS['nav_selected']) {
		$selected = "NAV_selected";
	} else {
		$selected = "";
	}
	if ($NAV_AZ_val === "0_9") {
		$NAV_AZ_dVal = NAV_getResource("0_9");
	}
	if ($NAV_AZ_val === "other") {
		$NAV_AZ_dVal = NAV_getResource("other");
	}
	if ($NAV_AZ_val === "all") {
		$NAV_AZ_dVal = NAV_getResource("all");
	}
	if ($NAV_AZ_val != $GLOBALS['nav_selected']) {
		// display all letters, only those with records has link;
		if (($GLOBALS['nav_linkRenderType']==3 && in_array($NAV_AZ_val, $GLOBALS['nav_arrLetters'])) || $GLOBALS['nav_linkRenderType']!=3) {
?>
			<li>
				<a href="<?php echo $GLOBALS['nav_currentPage'] . $GLOBALS['nav_queryString'] . urlencode($NAV_AZ_val); ?>" class="<?php echo $selected; ?>"><?php echo $NAV_AZ_dVal; ?></a>
			</li>
	<?php	if ($show_pipe) { ?>
			<li>
				|
			</li>
	<?php 	} ?>
<?php 	} else { ?>
			<li class="NAV_disabled">
				<?php echo $NAV_AZ_dVal; ?>
			</li>
	<?php	if ($show_pipe) { ?>
			<li>
				|
			</li>
	<?php 	} ?>			
<?php 	} ?>
<?php } else { ?>
			<li class="NAV_selected">
				<?php echo $NAV_AZ_dVal; ?>
			</li>
	<?php	if ($show_pipe) { ?>
			<li>
			 &nbsp;|
			</li>
	<?php 	} ?>			
<?php
	}
}
?>
	</ul>
</div>