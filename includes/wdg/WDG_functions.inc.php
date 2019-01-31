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

function WDG_phparray2jsarray($obj) { //FORCE the frst level to be an array
	$tm = '';
	if (is_array($obj)) {
		if (count($obj) == 0) {
			$tm = '[]';
		} else {
			$tm .= '[';
			foreach ($obj as $v) {
				$tm .= WDG_phparray2jsarray($v) . ',';
			}
			$tm = substr($tm, 0, strlen($tm)-1);
			$tm .= ']';
		}
	} else  if (is_scalar($obj) || is_null($obj)) {
		$tm .= '"' . addcslashes($obj,  "\\\"\r\n") . '"';
	} else {
		$tmp = '[]';
	}
	return $tm;
}

function WDG_php2js($obj) {
	$tm = '';
	if (is_array($obj)) {
		if (count($obj) == 0) {
			$tm = '[]'; //can't tell if it's array or hash
		} else {
			$tm .= '{';
			foreach ($obj as $k => $v) {
				$tm .= sprintf('"%s" : %s,', addcslashes($k, "\\\"\r\n"), WDG_php2js($v));
			}
			$tm = substr($tm, 0, strlen($tm)-1);
			$tm .= '}';
		}
	} else if (is_scalar($obj) || is_null($obj)) {
		$tm .= '"' . addcslashes($obj,  "\\\"\r\n") . '"';
	}
	return $tm;
}

function WDG_registerRecordInsert($connectionName, $rsName, $idField, $updateField) {
	if (!isset($GLOBALS['KT_dynamicInputSW'])) {
		$WDG_sessInsTest = array();
		$GLOBALS['WDG_sessInsTest'] = $WDG_sessInsTest;
		$_SESSION['WDG_sessInsTest'] = $WDG_sessInsTest;
		$GLOBALS['KT_dynamicInputSW'] = true;
	}

	//the sql query (string)
	$sql_query = $GLOBALS['query_' . $rsName];
	preg_match("/\sfrom\s*([^\s]+)?\s*/i", $sql_query, $sqlTable);
	$sqlTable = $sqlTable[1];

	$WDG_sessInsTest = &$_SESSION['WDG_sessInsTest'];
	$WDG_sessInsTest[] = array(
		'conn' => $connectionName,
		'rsName' => $rsName,
		'table' => $sqlTable,
		'idfield' => $idField,
		'updatefield' => $updateField
	);
	$GLOBALS['WDG_sessInsTest'] = $WDG_sessInsTest;
}

?>