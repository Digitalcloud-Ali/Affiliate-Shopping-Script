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

class TOR_SetOrder 
{
	
	var $connection;
	var $tableName;
	var $pk;
	var $pkType;				// STRING_TYPE or NUMERIC_TYPE
	var $orderField;
	var $orderPostField = '';
	var $doRedirectOnExec = true;
	
	function TOR_SetOrder(&$connection, $tableName, $pk, $pkType, $orderField, $orderPostField) {
		$this->connection = &$connection;
		$this->tableName =$tableName;
		$this->pk = $pk;
		$this->pkType = $pkType;
		$this->orderField = $orderField;
		$this->orderPostField = $orderPostField;
		$this->doRedirectOnExec = true;
		
		KT_setDbType($this->connection);
		//KT_SessionKtBack(KT_getFullUri());
	}

	function scriptDefinition() {
			return "
<script type=\"text/javascript\">
\$NXT_MOVE_SETTINGS = {
	orderfield: '" .$this->orderPostField. "'
}
</script>
			";
	}

	function setDoRedirect($flag) {
		$this->doRedirectOnExec = $flag;
	}

	function getOrderFieldName() {
		return 'order_' . $this->pk . '_' . $this->orderField;
	}

	function getOrderFieldValue($obj) {
		if (is_object($obj)) {
			$order = $obj->Fields($this->orderField);
		} else {
			$order = $obj[$this->orderField];
		}
		return $order . '|' . $order;
	}

	function Execute() {
		if (isset($_POST[$this->orderPostField])) {
			$permArr = array();
			$arr = explode(',', $_POST[$this->orderPostField]);
			if (count($arr) > 0) {
				foreach ($arr as $key => $val) {
					$arrParts = explode('|', $val);
					if (count($arrParts)==3 &&  $arrParts[1] != $arrParts[2]) {
						$permArr[] = $arrParts;
					}
				}
				$n = count($permArr);
				if ($n >0) {
					$sql = 'SELECT MAX('.KT_escapeFieldName($this->orderField).') +1 AS kt_tor_max FROM '. $this->tableName;
					$rs = $this->connection->Execute($sql) or die("Internal Error. Table Order:<br/>\n".$this->connection->ErrorMsg());
					$max = (int)$rs->Fields('kt_tor_max');
					for($i=0;$i<count($permArr);$i++) {
						$this->UpdateOrder($permArr[$i][0], $permArr[$i][1]+$max);
					}
					for($i=0;$i<count($permArr);$i++) {
						$this->UpdateOrder($permArr[$i][0], $permArr[$i][2]);
					}
				}
			}
			if ($this->doRedirectOnExec) {
				KT_redir(KT_getFullUri());
			}
		}
	}
	
	function UpdateOrder($id, $order) {
		$sql = 'UPDATE '. $this->tableName .' SET '. KT_escapeFieldName($this->orderField) .' = '. KT_escapeForSql($order,"NUMERIC_TYPE") .' WHERE '. KT_escapeFieldName($this->pk) .' = '. KT_escapeForSql($id, $this->pkType);
		$this->connection->Execute($sql) or die("Internal Error. Table Order:<br/>\n".$this->connection->ErrorMsg());
	}
	
}

?>