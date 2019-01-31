<div class="KT_textnav clearfix">
  <ul>
<?php
foreach ($GLOBALS['nav_arrCategory'] as $NAV_DEP_val) {
	if ($NAV_DEP_val=='' || trim($NAV_DEP_val)=='') {
		$NAV_DEP_dVal = '**';
	} else {
		$NAV_DEP_dVal = $NAV_DEP_val;
	}
	if ($NAV_DEP_val == $GLOBALS['nav_selected']) {
		$selected = 'NAV_selected';
	} else {
		$selected = '';
	}
	if ($NAV_DEP_val === 'all') {
		$NAV_DEP_dVal = NAV_getResource('all');
	}
	if ($NAV_DEP_val != $GLOBALS['nav_selected']) {
		// display all letters, only those with records has link;
		if ( ($GLOBALS['nav_linkRenderType']==3 && in_array($NAV_DEP_val, $GLOBALS['nav_arrCategoryWithRec'])) || $GLOBALS['nav_linkRenderType']!=3 ) {
?>
			<li>
				<a href="<?php echo $GLOBALS['nav_currentPage'] . $GLOBALS['nav_queryString'] . urlencode($NAV_DEP_val) ?>" class="<?php echo $selected; ?>"><?php echo $NAV_DEP_dVal; ?></a>
			</li>
<?php 	} else { ?>	
			<li class="NAV_disabled">
				<?php echo $NAV_DEP_dVal; ?>
			</li>
<?php 	} ?>
<?php } else { ?>
			<li class="NAV_selected">
				<?php echo $NAV_DEP_dVal; ?>
			</li>
<?php
	}
}
?>
	</ul>
</div>