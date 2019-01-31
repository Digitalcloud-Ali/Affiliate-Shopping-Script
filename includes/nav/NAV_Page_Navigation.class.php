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

	class NAV_Page_Navigation extends NAV_Regular {
		var $noPagesToDisplay = 3;

		function NAV_Page_Navigation($navName, $rsName, $relPath, $currentPage, $maxRows, $noPagesToDisplay) {
			parent::NAV_Regular($navName, $rsName, $relPath, $currentPage, $maxRows);
			$this->noPagesToDisplay = $noPagesToDisplay;
		}
		function Prepare() {
			parent::Prepare();
			$GLOBALS['nav_noPagesToDisplay']     = $this->noPagesToDisplay;
		}
	}
?>