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

class TFI_TableFilter {
	var $columns = array();
	var $filterName;

	function TFI_TableFilter($connection, $filterName) {
		KT_setDbType($connection);
		$this->filterName = $filterName;
		
		KT_SessionKtBack(KT_getFullUri());
		$_SESSION['KT_lastUsedList'] = substr($filterName,4);
	}
	
	function addColumn($colName, $type, $reference, $compareType, $defaultValue = '') {
		if (!isset($this->columns[$colName])) {
			$this->columns[$colName] = array();
		}
		
		$this->columns[$colName][$reference] = array();
		$this->columns[$colName][$reference]['type'] = $type;
		$this->columns[$colName][$reference]['reference'] = $this->filterName . "_" . $reference;
		$this->columns[$colName][$reference]['compareType'] = $compareType;
		
		if ($defaultValue != '') {
			$details = array();
			$details['type'] = $type;
			$details['method'] = 'VALUE';
			$details['reference'] = $defaultValue;
			tNG_prepareValues($details);
			$defaultValue = $details['value'];
		}
		if ( !isset($_SESSION[$this->filterName . "_" . $reference]) ) {
			$_SESSION[$this->filterName . "_" . $reference] = $defaultValue;
		}
	}
	
	function Execute() {
		$show_filter_reference = "show_filter_" . $this->filterName;
		$reset_filter_reference = "reset_filter_" . $this->filterName;
		$has_filter_reference = "has_filter_" . $this->filterName;
		$filter_reference = "filter_" . $this->filterName;
		
		if (isset($_GET[$show_filter_reference])) {
			$_SESSION[$has_filter_reference] = 1;
			$url = KT_addReplaceParam(KT_getFullUri(), $show_filter_reference);
			KT_redir($url);
		}
		if (isset($_GET[$reset_filter_reference])) {
			unset($_SESSION[$reset_filter_reference]);
			unset($_SESSION[$has_filter_reference]);
			unset($_SESSION[$filter_reference]);
			foreach($this->columns as $key => $columnDetails) {
				foreach ($columnDetails as $key => $details) {
					$_SESSION[$details['reference']] = '';
				}
			}
			$url = KT_addReplaceParam(KT_getFullUri(), $reset_filter_reference);
			KT_redir($url);
		}

		if (sizeof($_POST) > 0 && isset($_POST[$this->filterName])) {
			foreach($this->columns as $columnName => $columnDetails) {
				foreach ($columnDetails as $key => $details) {
					$variableName = $details['reference'];
					if (isset($_POST[$variableName])) {
						$details['method'] = 'POST';
						if ($details['type'] == 'DATE_TYPE' || $details['type'] == 'DATE_ACCESS_TYPE') {
							$details['type'] = 'STRING_TYPE';
							tNG_prepareValues($details);
						} else {
							tNG_prepareValues($details);
						}
						$_SESSION[$variableName] = $details['value'];
					} else {
						$_SESSION[$variableName] = '';
					}
				}
			}
			$url = KT_getFullUri();
			$url = KT_addReplaceParam($url, '/pageNum_.*/');
			$url = KT_addReplaceParam($url, '/totalRows_.*/');
			KT_redir($url);
		}
		
		$condition = '';
		foreach($this->columns as $columnName => $columnDetails) {
			foreach ($columnDetails as $key => $details) {
				$variableName = $details['reference'];
				$details['value'] = @$_SESSION[$variableName];
				if ( !isset($details['value']) || $details['value'] == '' ) {
					continue;
				}
				if ($condition != '') {
					$condition .= " AND ";
				}
				$variableValue = trim($details['value']);
				$compareType = $details['compareType'];
				switch ($details['type']) {
					case 'NUMERIC_TYPE':
					case 'DOUBLE_TYPE':
						// if decimal separator is , => .
						$variableValue = str_replace(',', '.', $variableValue);
						if (preg_match('/^(<|>|=|<=|>=|=<|=>|<>|!=)\s?-?\d*\.?\d+$/', $variableValue, $matches)) {
							$modifier = trim($matches[1]);
							if ($modifier == '!=') {
								$modifier = '<>';
							}
							$variableValue = trim(substr($variableValue, strlen($modifier)));
							$condition .= KT_escapeFieldName($columnName) . ' ' . $modifier . ' ' . $variableValue;
						} else {
							$condition .= KT_escapeFieldName($columnName) . ' ' . $compareType . ' ' . KT_escapeForSql($variableValue, $details['type']);
						}
						break;
					case 'CHECKBOX_1_0_TYPE':
					case 'CHECKBOX_-1_0_TYPE':
						if (preg_match('/^[<>]{1}\s?-?\d*\.?\d+$/', $variableValue)) {
							$condition .= KT_escapeFieldName($columnName) . $variableValue;
						} else {
							$condition .= KT_escapeFieldName($columnName) . " = " . KT_escapeForSql($variableValue, $details['type']);
						}
						break;
					case 'DATE_TYPE':
					case 'DATE_ACCESS_TYPE':
						$localCond = $this->prepareDateCondition($columnName, $details);
						if ($localCond != '') {
							$condition .= $localCond;
						} else if(strlen($condition) > 0){
							// if the date entered is invalid, we will not add it to the condition
							$condition = substr($condition, 0, strlen($condition)-5);
						}
						break;
					default:
						switch ($compareType) {
							case '=':
								break;
							case 'A%':
								$variableValue = $variableValue . '%';
								$compareType = 'LIKE';
								break;
							case '%A':
								$variableValue = '%' . $variableValue;
								$compareType = 'LIKE';
								break;
							default :
								$variableValue = '%' . $variableValue . '%';
								$compareType = 'LIKE';
								break;
						}
						$variableValue = KT_escapeForSql($variableValue, $details['type']);
						$condition .= KT_escapeFieldName($columnName) . ' ' . $compareType . ' ' . $variableValue;
						break;
				}
			}
		}
		if ($condition == '') {
			$condition = '1=1';
		}
		$condition = str_replace("%","%%",$condition);
		$_SESSION[$filter_reference] = $condition;
	}

	function getShowFilterLink() {
		$show_filter_reference = "show_filter_" . $this->filterName;
		
		if (isset($_GET[$show_filter_reference])) {
			$url = KT_addReplaceParam(KT_getFullUri(), $show_filter_reference);
		} else {
			$url = KT_addReplaceParam(KT_getFullUri(), $show_filter_reference, "1");
		}
		
		return $url;
	}
	
	function getResetFilterLink() {
		$reset_filter_reference = "reset_filter_" . $this->filterName;
		
		if (isset($_GET[$reset_filter_reference])) {
			$url = KT_addReplaceParam(KT_getFullUri(), $reset_filter_reference);
		} else {
			$url = KT_addReplaceParam(KT_getFullUri(), $reset_filter_reference, "1");
			$url = KT_addReplaceParam($url, '/pageNum_.*/');
			$url = KT_addReplaceParam($url, '/totalRows_.*/');
		}
		
		return $url;
	}
	
	function prepareDateCondition($columnName, &$arr) {
		$year = '';
		$month = '';
		$day = '';
		$hour = '';
		$min = '';
		$sec = '';
		
		$value = '';
		$dateType = '';
		$modifier = '';
		
		$date1 = '';
		$date2 = '';
		$compareType1 = '';
		$compareType2 = '';
		$condJoin = '';
		
		$cond = '';
		$myDate = '';
		$dateArr = array();
		
		$value = $arr['value'];
		
		if (!isset($GLOBALS['KT_db_time_format_internal'])) {
			KT_getInternalTimeFormat();
		}
		
		// extract modifier and date from value
		if ( preg_match('/^(<|>|=|<=|>=|=<|=>|<>|!=)\s*\d+.*$/', $value, $matches) ) {
			$modifier = trim($matches[1]);
			$value = trim(substr($value, strlen($modifier)));
		} elseif ( preg_match('/^[^\d]+/', $value) ) {
			$ret = '';
			return $ret;
		}
		
		// prepare modifier for databases that do not support !=
		if ($modifier == '!=') {
			$modifier = '<>';
		}
		
		
		
		/* date pieces isolation */
		
		// year only
		if ( preg_match('/^\d+$/', $value) ) {
			$dateType = 'y';
			$year = $value;
		}
		
		// year month
		if ( preg_match('/^\d+[-\/\[\]\(\)\*\|\+\.=,]{1}\d+$/', $value) ) {
			$dateType = 'm';
			$dateArr = preg_split('/([-\/\[\]\(\)\*\|\+\.=,])/', $value, -1, PREG_SPLIT_NO_EMPTY);
			$month = $dateArr[0];
			$year = $dateArr[1];
			if (strlen($month) > 2) {
				$month = $dateArr[1];
				$year = $dateArr[0];
			}
		}
		
		// full date (year, month, day)
		if ( preg_match('/^\d+[-\/\[\]\(\)\*\|\+\.=,]{1}\d+[-\/\[\]\(\)\*\|\+\.=,]{1}\d+$/', $value) ) {
			$dateType = 'd';
			list($year, $month, $day) = $this->getDateParts($value);
		}
		
		// full date & hour
		if ( preg_match('/^\d+[-\/\[\]\(\)\*\|\+\.=,]{1}\d+[-\/\[\]\(\)\*\|\+\.=,]{1}\d+\s+\d+[^\d]*$/', $value) ) {
			$dateType = 'h';
			$myParts = strpos($value, ' ');
			$datePart = substr($value, 0, $myParts);
			$timePart = substr($value, $myParts + 1);
			list($year, $month, $day) = $this->getDateParts($datePart);
			list($hour, $min, $sec) = $this->getTimeParts($timePart, 'HH');
		}
		
		// full date + hour, minutes
		if ( preg_match('/^\d+[-\/\[\]\(\)\*\|\+\.=,]{1}\d+[-\/\[\]\(\)\*\|\+\.=,]{1}\d+\s+\d+:\d+[^\d]*$/', $value) ) {
			$dateType = 'i';
			$myParts = strpos($value, ' ');
			$datePart = substr($value, 0, $myParts);
			$timePart = substr($value, $myParts + 1);
			list($year, $month, $day) = $this->getDateParts($datePart);
			list($hour, $min, $sec) = $this->getTimeParts($timePart, 'HH:ii');
		}
		
		// full date time
		if ( preg_match('/^\d+[-\/\[\]\(\)\*\|\+\.=,]{1}\d+[-\/\[\]\(\)\*\|\+\.=,]{1}\d+\s+\d+:\d+:\d+[^\d]*$/', $value) ) {
			$dateType = 's';
			$myParts = strpos($value, ' ');
			$datePart = substr($value, 0, $myParts);
			$timePart = substr($value, $myParts + 1);
			list($year, $month, $day) = $this->getDateParts($datePart);
			list($hour, $min, $sec) = $this->getTimeParts($timePart, 'HH:ii:ss');
		}
		
		if ($dateType == '') {
			$dateType = 't';
			$value = KT_formatDate2DB($value);
		}
		
		/* prepare date parts */
		
		// 1 or 2 digits year
		if ( preg_match('/^\d{1,2}$/', $year) ) {
			if ($year < 70) {
				$year = 2000 + $year;
			} else {
				$year = 1900 + $year;
			}
		}
		
		if ( $month < 1 || $month > 12 ) {
			$month = '01';
		}
		if ( $hour > 23 ) {
			$hour = '00';
		}
		if ( $min > 59 ) {
			$min = '00';
		}
		if ( $sec > 59 ) {
			$sec = '00';
		}

		/* prepare condition operators based on modifiers */
		switch ($modifier) {
			case '>=':
				$compareType1 = '>=';
				$compareType2 = '';
				$condJoin = '';
				break;
			case '<=':
				$compareType1 = '';
				$compareType2 = '<=';
				$condJoin = '';
				break;
			case '<':
				$compareType1 = '<';
				$compareType2 = '';
				$condJoin = '';
				break;
			case '>':
				$compareType1 = '';
				$compareType2 = '>';
				$condJoin = '';
				break;
			case '<>':
				$compareType1 = '<';
				$compareType2 = '>';
				$condJoin = 'OR';
				break;
			default:
				$compareType1 = '>=';
				$compareType2 = '<=';
				$condJoin = 'AND';
				break;
		}
		
		/* prepare dates for filtering */
		switch ($dateType) {
			case 'y':
				$date1 = KT_convertDate($year . '-01-01', 'yyyy-mm-dd', $GLOBALS['KT_db_date_format']);
				$date2 = KT_convertDate($year . '-12-31', 'yyyy-mm-dd', $GLOBALS['KT_db_date_format']);
				break;
			case 'm':
				$date1 = KT_convertDate($year . '-' . $month . '-01', 'yyyy-mm-dd', $GLOBALS['KT_db_date_format']);
				$maxday = KT_getDaysOfMonth($month, $year);
				$date2 = KT_convertDate($year . '-' . $month . '-' . $maxday, 'yyyy-mm-dd', $GLOBALS['KT_db_date_format']);
				break;
			case 'd':
				$date1 = KT_convertDate($year . '-' . $month . '-' . $day . ' 00:00:00', 'yyyy-mm-dd HH:ii:ss', $GLOBALS['KT_db_date_format'] . ' ' . $GLOBALS['KT_db_time_format_internal']);
				$date2 = KT_convertDate($year . '-' . $month . '-' . $day . ' 23:59:59', 'yyyy-mm-dd HH:ii:ss', $GLOBALS['KT_db_date_format'] . ' ' . $GLOBALS['KT_db_time_format_internal']);
				break;
			case 'h':
				$date1 = KT_convertDate($year . '-' . $month . '-' . $day . ' ' . $hour . ':00:00', 'yyyy-mm-dd HH:ii:ss', $GLOBALS['KT_db_date_format'] . ' ' . $GLOBALS['KT_db_time_format_internal']);
				$date2 = KT_convertDate($year . '-' . $month . '-' . $day . ' ' . $hour . ':59:59', 'yyyy-mm-dd HH:ii:ss', $GLOBALS['KT_db_date_format'] . ' ' . $GLOBALS['KT_db_time_format_internal']);
				break;
			case 'i':
				$date1 = KT_convertDate($year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $min . ':00', 'yyyy-mm-dd HH:ii:ss', $GLOBALS['KT_db_date_format'] . ' ' . $GLOBALS['KT_db_time_format_internal']);
				$date2 = KT_convertDate($year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $min . ':59', 'yyyy-mm-dd HH:ii:ss', $GLOBALS['KT_db_date_format'] . ' ' . $GLOBALS['KT_db_time_format_internal']);
				break;
			case 's':
				$date1 = KT_convertDate($year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $min . ':' . $sec, 'yyyy-mm-dd HH:ii:ss', $GLOBALS['KT_db_date_format'] . ' ' . $GLOBALS['KT_db_time_format_internal']);
				$date2 = KT_convertDate($year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $min . ':' . $sec, 'yyyy-mm-dd HH:ii:ss', $GLOBALS['KT_db_date_format'] . ' ' . $GLOBALS['KT_db_time_format_internal']);
				$compareType1 = '=';
				$compareType2 = '';
				$condJoin = '';
				break;
			case 't':
				$date1 = $value;
				$date2 = '';
				$compareType1 = '=';
				$compareType2 = '';
				$condJoin = '';
				break;
			default:
				$dateType = '';
				$compareType1 = '';
				$compareType2 = '';
				$condJoin = '';
				break;
		}
		
		if ($dateType != '') {
			$cond = '(';
			if ($compareType1 != '') {
				$cond .= KT_escapeFieldName($columnName) . ' ' . $compareType1 . ' ' . KT_escapeForSql($date1, $arr['type']);
			}
			if ($compareType2 != '') {
				if ($compareType1 != '') {
					$cond .= ' ' . $condJoin . ' ';
				}
				$cond .= KT_escapeFieldName($columnName) . ' ' . $compareType2 . ' ' . KT_escapeForSql($date2, $arr['type']);
			}
			$cond .= ')';
		}
		
		return $cond;
	}
	
	function getDateParts($datePart) {
		$myDate = '';
		$dateArr = array();
		$year = '';
		$month = '';
		$day = '';
		
		$myDate = KT_convertDate($datePart, $GLOBALS['KT_screen_date_format'], 'yyyy-mm-dd');
		$dateArr = explode('-', $myDate);
		$year = $dateArr[0];
		$month = $dateArr[1];
		$day = $dateArr[2];
		if ( $month < 1 || $month > 12 ) {
			$month = '01';
		}
		$maxday = KT_getDaysOfMonth($month, $year);
		if ( $day < 1 || $day > $maxday ) {
			$day = '01';
		}
		return array($year, $month, $day);
	}
	
	function getTimeParts($timePart, $format) {
		$myDate = '';
		$dateArr = array();
		$hour = '';
		$min = '';
		$sec = '';
		
		$myDate = KT_convertDate($timePart, $GLOBALS['KT_screen_time_format_internal'], $format);
		$dateArr = explode(':', $myDate);
		$hour = $dateArr[0];
		if (isset($dateArr[1])) {
			$min = $dateArr[1];
		}
		if (isset($dateArr[2])) {
			$sec = $dateArr[2];
		}
		if ( $format != 'HH:ii:ss' && preg_match('/p/i', $timePart) && $hour < 12) {
			$hour += 12;
		}
		return array($hour, $min, $sec);
	}
	
}
?>