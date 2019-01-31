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

class NAV_Category
{
	var $connection;
	var $rsName;
	var $tableName;
	var $pk;
	var $fieldName;
	var $tableDetail;
	var $fk;
	var $isNumeric;
	var $linkRenderType;
	var $arrCategory = array();
	var $arrCategoryWithRec = array();
	var $relPath = "";
	var $currentPage = "";
	var $selected = "";
	
	function NAV_Category(&$connection, $rsName, $tableName, $pk, $fieldName, $tableDetail, $fk, $isNumeric, $relPath, $currentPage, $linkRenderType) {
		$this->connection = &$connection; 
		$this->rsName = $rsName;
		$this->tableName = $tableName;
		$this->pk = $pk;
		$this->fieldName = $fieldName;
		$this->tableDetail = $tableDetail;
		$this->fk = $fk;
		$this->isNumeric = $isNumeric;
		$this->linkRenderType = $linkRenderType;
		$this->relPath = $relPath;
		$this->currentPage = $currentPage;
		$this->getVarName = "KT_" . preg_replace("/[^\w]+/", "", $this->tableName);
	}

	function getCondition() {
		$all_string = "all";
		$condition = '1=1';
		// only records with link;
		if ($this->linkRenderType==1) {
			$sql = 'SELECT DISTINCT a.'.$this->pk.', a.'.$this->fieldName.' FROM '.$this->tableName.' a INNER JOIN '.$this->tableDetail.' b ON a.'.$this->pk.'=b.'.$this->fk.' ORDER BY a.'.$this->fieldName;
		} else {
			$sql = 'SELECT DISTINCT '.$this->pk.', '.$this->fieldName.' FROM '.$this->tableName.' ORDER BY '.$this->fieldName;
		}
		$this->arrCategory = $this->getRecords($sql);
		$sql = 'SELECT DISTINCT a.'.$this->pk.', a.'.$this->fieldName.' FROM '.$this->tableName.' a INNER JOIN '.$this->tableDetail.' b ON a.'.$this->pk.'=b.'.$this->fk.' ORDER BY a.'.$this->fieldName;
		$this->arrCategoryWithRec = $this->getRecords($sql);
		
		if (count($this->arrCategory)>0) {
			if (isset($_GET[$this->getVarName])) {
				$needle = KT_getRealValue("GET", $this->getVarName);
				if ($needle !== $all_string && in_array($needle, $this->arrCategory)) {
					$cond = array_search($needle, $this->arrCategory);
				}
			} else {
				$arr = $this->arrCategory;
				if (count($this->arrCategoryWithRec)>0) {
					$arr = $this->arrCategoryWithRec;
				}
				$needle = array_shift($arr);
				$cond = array_search($needle, $this->arrCategory);
			}
			$this->arrCategory[] = $all_string;
			if ($this->linkRenderType == 3) {
				$this->arrCategoryWithRec[] = $all_string;
			}
		}
		$this->selected = $needle;
		$this->checkBoundries();
		if (isset($cond)) {
			if (!$this->isNumeric) {
				$condition = ' '.$this->fk.'='. KT_escapeForSql($cond, "STRING_TYPE"). ' ';
			} else {
				$condition = ' '.$this->fk.'='. KT_escapeForSql($cond, "NUMERIC_TYPE"). ' ';
			}
		}
		$condition = str_replace("%","%%",$condition);
		return $condition;
	}
	
	function Prepare() {
		$queryString = $_SERVER['QUERY_STRING'];
		$queryString = KT_addReplaceParam($queryString, $this->getVarName);
		$queryString = KT_addReplaceParam($queryString, '/pageNum_'.preg_quote($this->rsName,'/').'/');
		$queryString = KT_addReplaceParam($queryString, '/totalRows_'.preg_quote($this->rsName,'/').'/');
		if ($queryString != '') {
			$queryString = '?' . $queryString . '&'.$this->getVarName.'=';
		} else {
			$queryString = '?'.$this->getVarName.'=';
		}
		
		$GLOBALS['nav_arrCategory'] = $this->arrCategory;
		$GLOBALS['nav_arrCategoryWithRec'] = $this->arrCategoryWithRec;
		$GLOBALS['nav_relPath'] = $this->relPath;
		$GLOBALS['nav_currentPage'] = $this->currentPage;
		$GLOBALS['nav_queryString'] = $queryString;
		$GLOBALS['nav_linkRenderType'] = $this->linkRenderType;
		$GLOBALS['nav_selected'] = $this->selected;
	}
	
	function getRecords($sql) {
		$arr = array();
		$rs = $this->connection->Execute($sql);
		if ($rs) {
			while (!$rs->EOF) {
				$arr[$rs->Fields($this->pk)] = $rs->Fields($this->fieldName);
				$rs->MoveNext();
			}
			natsort($arr);
			
		}
		return $arr;
	}
	
	function checkBoundries() {
		if (isset($_GET[$this->getVarName])) {
			$needle = KT_getRealValue("GET", $this->getVarName);
			if (!in_array($needle, $this->arrCategory)) {
				$KT_url = KT_getFullUri();
				$KT_url = KT_addReplaceParam($KT_url, $this->getVarName);
				KT_redir($KT_url);
			}
		}
	}
	
}

?>