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

class TSO_TableSorter {
	var $columns = array();
	var $rsName;
	var $sorterName;
	var $defaultColumn = "";

	function TSO_TableSorter($rsName, $sorterName) {
		$this->rsName = $rsName;
		$this->sorterName = $sorterName;
		
		KT_SessionKtBack(KT_getFullUri());
		$_SESSION['KT_lastUsedList'] = substr($sorterName,4);
	}

	function addColumn($colName) {
		if (!isset($this->columns[$colName])) {
			$this->columns[$colName] = true;
		}
	}

	function setDefault($defaultColumn) {
		$this->defaultColumn = $defaultColumn;
		$sorter_reference = "sorter_" . $this->sorterName;
		if (!isset($_SESSION[$sorter_reference])) {
			$_SESSION[$sorter_reference] = $defaultColumn;
		}
	}

	function Execute() {
		$sorter_reference = "sorter_" . $this->sorterName;
		
		if (isset($_GET[$sorter_reference])) {
			$sorterString = $_GET[$sorter_reference];
			$columnName = str_replace(" DESC", "", $sorterString);
			if (isset($this->columns[$columnName])) {
				$_SESSION[$sorter_reference]=$_GET[$sorter_reference];
				$url = KT_addReplaceParam(KT_getFullUri(), $sorter_reference);
				KT_redir($url);
			}
		}
	}

	// Get Current Sort
	function getCurrentSort() {
		$value = $this->defaultColumn;
		$sorter_reference = "sorter_" . $this->sorterName;
	  if (isset($_SESSION[$sorter_reference])) {
			$value = $_SESSION[$sorter_reference];
	  }
	  return $value;
	}

	//Get Sort Icon Function
	function getSortIcon($column){
	  $value = $this->getCurrentSort();
		
	  if ($value == $column) {
			return 'KT_asc';
	  } elseif ($value == $column.' DESC') {
			return 'KT_desc';
	  }
	}

	//Get Sort Link Function
	function getSortLink($column) {
		$sorter_reference = "sorter_" . $this->sorterName;
		
	  $value = $this->getCurrentSort();
	  $paramVal = $column;  
	  if($value == $column){
			$paramVal .= " DESC";
		}
	  $url = KT_addReplaceParam(KT_getFullUri(), $sorter_reference, $paramVal);
	  return $url;
	}
}
?>