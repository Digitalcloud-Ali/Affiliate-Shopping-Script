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

class NAV_AZ
{
	
	var $connection;
	var $rsName;
	var $tableName;
	var $fieldName;
	var $linkRenderType;
	var $arrLetters = array();
	var $relPath = "";
	var $currentPage = "";
	var $selected = "";
	
	var $hasOther;
	
	function NAV_AZ(&$connection, $rsName, $tableName, $fieldName, $relPath, $currentPage, $linkRenderType, $useNumbers) {
		$this->connection = &$connection; 
		$this->rsName = $rsName;
		$this->tableName = $tableName;
		$this->fieldName = $fieldName;
		$this->relPath = $relPath;
		$this->currentPage = $currentPage;
		$this->linkRenderType = $linkRenderType;
		$this->useNumbers = $useNumbers;
		
		$this->hasOther = false;
		$this->hasNumbers = false;
	}

	function getCondition() {
		$other_string = "other";
		$number_string = "0_9";
		$all_string = "all";
		$allowed = range('A','Z');
		$numbers_allowed = array('0','1','2','3','4','5','6','7','8','9');
		$condition = '1=1';
		$arr = array();
		$rs = $this->connection->Execute('SELECT DISTINCT '.$this->fieldName.' FROM '.$this->tableName);
		if ($rs) {
			while (!$rs->EOF) {
				$needle = strtoupper(substr($rs->Fields($this->fieldName), 0, 1));
				if (in_array($needle, $allowed)) {
					if (!in_array($needle, $arr)) {
						$arr[] = $needle;
					}
				} elseif ($this->useNumbers === true && in_array($needle, $numbers_allowed)) {
					$this->hasNumbers = true;
				} else {
					$this->hasOther = true;
				}
				$rs->MoveNext();
			}
			natsort($arr);
			$this->arrLetters = $arr;
		}
		if ($this->hasNumbers) {
			$this->arrLetters[] = $number_string;
		}
		if ($this->hasOther) {
			$this->arrLetters[] = $other_string;
		}
		if (count($this->arrLetters) > 0) {
			$this->arrLetters[] = $all_string;
		}
		$other_selected = false;
		$numbers_selected = false;
		$all_selected = false;
		if (isset($_GET['KT_az'])) {
			$cond = KT_getRealValue("GET", "KT_az");
			if (strtolower($cond) == $other_string) {
				$other_selected = true;
			}
			if (strtolower($cond) == $number_string) {
				$numbers_selected = true;
			}
			if (strtolower($cond) == $all_string) {
				$all_selected = true;
			}
		} else {
			if (count($this->arrLetters)>0) {
				$cond = array_shift($arr);
			}
		}
		$this->selected = $cond;
		if ($numbers_selected) {
			$this->selected = $number_string;
		}
		if ($other_selected) {
			$this->selected = $other_string;
		}
		if ($all_selected) {
			$this->selected = $all_string;
			$cond = null;
		}
		$this->checkBoundries();
		if (isset($cond)) {
			if (!$other_selected && !$numbers_selected) {
				$condition = ' ('.$this->fieldName.' LIKE '.strtoupper( KT_escapeForSql($cond . "%", "STRING_TYPE") ).' OR '.$this->fieldName.' LIKE '.strtolower( KT_escapeForSql($cond . "%", "STRING_TYPE") ).') ';
			} elseif ($numbers_selected) {
				$condition = ' (';
				for ($i=0; $i<count($numbers_allowed); $i++) {
					if ($i != 0) {
						$condition .= ' OR ';
					}
					$condition .= $this->fieldName.' LIKE \''.$numbers_allowed[$i].'%\'';
				}
				$condition .= ') ';
			} else {
				$condition = ' (';
				$tmp_arr = $allowed;
				if ($this->useNumbers) {
					for ($i=0; $i<count($numbers_allowed); $i++) {
						$tmp_arr[] = $numbers_allowed[$i];
					}
				}
				for ($i=0; $i<count($tmp_arr); $i++) {
					if ($i != 0) {
						$condition .= ' AND ';
					}
					$condition .= $this->fieldName.' NOT LIKE \''.$tmp_arr[$i].'%\'';
				}
				$condition .= ') ';
			}
		}
		$condition = str_replace("%","%%",$condition);
		return $condition;
	}
	
	function Prepare() {
		$queryString = $_SERVER['QUERY_STRING'];
		$queryString = KT_addReplaceParam($queryString, 'KT_az');
		$queryString = KT_addReplaceParam($queryString, '/pageNum_'.preg_quote($this->rsName,'/').'/');
		$queryString = KT_addReplaceParam($queryString, '/totalRows_'.preg_quote($this->rsName,'/').'/');
		if ($queryString != '') {
			$queryString = '?' . $queryString . '&KT_az=';
		} else {
			$queryString = '?KT_az=';
		}	
		
		$GLOBALS['nav_arrLetters'] = $this->arrLetters;
		$GLOBALS['nav_relPath'] = $this->relPath;
		$GLOBALS['nav_currentPage'] = $this->currentPage;
		$GLOBALS['nav_queryString'] = $queryString;
		$GLOBALS['nav_linkRenderType'] = $this->linkRenderType;
		$GLOBALS['nav_selected'] = $this->selected;
		$GLOBALS['nav_hasOther'] = $this->hasOther;
		$GLOBALS['nav_useNumbers'] = $this->useNumbers;
	}
	
	function checkBoundries() {
		
		if (isset($_GET['KT_az'])) {
			$cond = KT_getRealValue("GET", "KT_az");
			$do_redirect = false;
			if ($this->linkRenderType!=2) {
				if (!in_array($cond, $this->arrLetters)) {
					$do_redirect = true;
				}
			}	else {
				$allowed = range('A','Z');
				if ($this->useNumbers) {
					$allowed[] = "0_9";
				}
				$allowed[] = "other";
				$allowed[] = "all";
				if (!in_array($cond, $allowed)) {
					$do_redirect = true;
				}
			}
			if ($do_redirect) {
				$KT_url = KT_getFullUri();
				$KT_url = KT_addReplaceParam($KT_url,'KT_az');
				KT_redir($KT_url);
			}
		}
	}

}

?>