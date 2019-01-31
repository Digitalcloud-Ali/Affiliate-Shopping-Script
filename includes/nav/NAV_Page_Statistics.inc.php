<script type="text/javascript">
	$NAV_Text_start = <?php echo ($GLOBALS['nav_totalRows'] == 0)?0:($GLOBALS['nav_pageNum'] * $GLOBALS['nav_maxRows'] + 1) ?>;
</script>
 <?php echo NAV_getResource("Page"); ?>
 <?php echo ($GLOBALS['nav_pageNum'] + 1) ?>
 <?php echo NAV_getResource("of"); ?>
 <?php echo ($GLOBALS['nav_totalPages'] + 1) ?>