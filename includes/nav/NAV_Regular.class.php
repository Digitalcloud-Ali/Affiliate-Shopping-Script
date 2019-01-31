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

	class NAV_Regular {
		var $navName = "";
		var $rsName = "";
		var $relPath = "";
		var $currentPage = "";
		var $maxRows = 0;
		var $pageNum = 0;
		var $totalPages = 0;
		var $totalRows = 0;

		function NAV_Regular($navName, $rsName, $relPath, $currentPage, $maxRows) {
			$this->navName = $navName;
			$this->rsName = $rsName;
			$this->relPath = $relPath;
			$this->currentPage = $currentPage;
		
			KT_session_start();
			if (!isset($_SESSION["default_max_rows_" . $navName])) {
			    $_SESSION["default_max_rows_" . $navName] = $maxRows;
			}
			if (!isset($_SESSION["max_rows_" . $navName])) {
				$_SESSION["max_rows_" . $navName] = (int)$maxRows;
			}
			if (isset($_GET["show_all_" . $this->navName])) {
				$_SESSION["max_rows_" . $navName] = 10000;
			} else {
				if (isset($_POST['KT_dynRows_'.$navName])) {
					$KT_dynRows = (int)$_POST['KT_dynRows_'.$navName];
					if ($KT_dynRows > 0) {
						$_SESSION["max_rows_" . $navName] = $KT_dynRows;
						$KT_url = KT_addReplaceParam(KT_getFullURI(),'pageNum_'.$this->rsName);
						KT_redir($KT_url);
					}
				} else {
					if ($_SESSION["default_max_rows_" . $navName] != $maxRows || $_SESSION["max_rows_" . $navName] == 10000) {
						$_SESSION["max_rows_" . $navName] = $maxRows;
						$_SESSION["default_max_rows_" . $navName] = $maxRows;
					}
				}
			}
		}
		
		function checkBoundries() {
			$this->maxRows = $GLOBALS['maxRows_'.$this->rsName];
			$this->pageNum = $GLOBALS['pageNum_'.$this->rsName];
			$this->totalPages = $GLOBALS['totalPages_'.$this->rsName];
			$this->totalRows = $GLOBALS['totalRows_'.$this->rsName];
			
			$KT_url = KT_getFullUri();
			$pageNum = $this->pageNum;
			$maxRows = $this->maxRows;
			$totalRows = $this->totalRows;
			
			if ($this->pageNum > $this->totalPages && $this->totalPages > -1) {
				$KT_url = KT_addReplaceParam($KT_url,'pageNum_'.$this->rsName,$this->totalPages);
				KT_redir($KT_url);
			}
			if ($this->pageNum < 0) {
				$KT_url = KT_addReplaceParam($KT_url,'pageNum_'.$this->rsName);
				KT_redir($KT_url);
			}
		}

		function Prepare() {
			$queryString = $_SERVER['QUERY_STRING'];
			$queryString = KT_addReplaceParam($queryString, 'pageNum_'.$this->rsName);
			$queryString = KT_addReplaceParam($queryString, 'totalRows_'.$this->rsName);
			if ($queryString!='') {
				$queryString = '&'.$queryString; 
			}
			$GLOBALS['nav_rsName']		= $this->rsName;
			$GLOBALS['nav_relPath'] 	= $this->relPath;
			$GLOBALS['nav_currentPage'] = $this->currentPage;
			$GLOBALS['nav_queryString'] = $queryString;

			$GLOBALS['nav_maxRows']     = $this->maxRows;
			$GLOBALS['nav_pageNum']     = $this->pageNum;
			$GLOBALS['nav_totalPages']  = $this->totalPages;
			$GLOBALS['nav_totalRows']   = $this->totalRows;
		}

		function getShowAllLink() {

			$show_all_reference = "show_all_" . $this->navName;

			if (isset($_GET[$show_all_reference])) {
				$url = KT_addReplaceParam(KT_getFullUri(), $show_all_reference);
			} else {
				$url = KT_addReplaceParam(KT_getFullUri(), $show_all_reference, "1");
			}
			$url = KT_addReplaceParam($url, 'pageNum_' . $this->rsName);
			$url = KT_addReplaceParam($url, 'totalRows_' . $this->rsName);

			return $url;
		}

	}

?>