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

/**
 * This class is the "insert" implementation of the tNG_multiple class.
 * @access public
 */
class tNG_multipleInsert extends tNG_multiple {
	/**
	 * number of the successful inserts;
	 * @param integer
	 * @access protected
	 */ 
	var $insertElements;
	/**
	 * if is false will check set ther error on each transaction and will reset the Number of transactions executed successfully
	 * @param boolean
	 * @access public
	 */
	var $executeSubSets;

	/**
	 * Constructor. Sets the connection, the database name and other default values.
	 * Also sets the transaction type.
	 * @param object KT_Connection &$connection the connection object
	 * @access public
	 */
	function tNG_multipleInsert(&$connection) {
		parent::tNG_multiple($connection);
		$this->transactionType = '_multipleInsert';
		$this->setInsertElements("GET","no_new");
		$this->exportRecordset = true;
	}
	
	/**
	 * setter. set the number of inserts will be made; 
	 * @param string $method
	 * @param string $reference
	 * @return nothing
	 */ 
	function setInsertElements($method, $reference) {
		$tmp = KT_getRealValue($method, $reference);
		if (is_null($tmp)) {
			$tmp = 1;
		} elseif ($tmp >20) {
			$tmp = 20;
		}
		$this->insertElements = $tmp;
	}
	
	/**
	 * Executes all sub-transactions
	 * @access protected
	 */
	function prepareSQL() {
		tNG_log::log('tNG_multipleInsert', 'prepareSQL', 'begin');
		$ret = null;
		$this->noSuccess = 0;
		$failed = false;
		for ($i=1;true;$i++) {
			$tmp = KT_getRealValue("POST", $this->pkName . "_" . $i);
			if (!isset($tmp)) {
				break;
			}
			$this->multTNGs[$i-1] = new tNG_insert($this->connection);
			$this->multTNGs[$i-1]->setDispatcher($this->dispatcher);
			$this->multTNGs[$i-1]->multipleIdx = $i;
			// register triggers
			for ($j=0;$j<sizeof($this->multTriggers);$j++) {
				call_user_func_array(array(&$this->multTNGs[$i-1], "registerConditionalTrigger"), $this->multTriggers[$j]);
			}
			// add columns
			$this->multTNGs[$i-1]->setTable($this->table);
			foreach($this->columns as $colName=>$colDetails) {

				if ($colDetails['method'] == 'VALUE') {
					$reference = $colDetails['reference'];
					$value = KT_getRealValue($colDetails['method'], $colDetails['reference']);
				} else {
					$reference = $colDetails['reference'] . "_" . $i;
					$value = KT_getRealValue($colDetails['method'], $colDetails['reference'] . "_" . $i);
					if (!isset($value)) {
						$reference = $colDetails['reference'];
						$value = KT_getRealValue($colDetails['method'], $colDetails['reference']);
					}
				}

				$this->columns[$colName]['value'] = $value;
				$this->multTNGs[$i-1]->addColumn($colName, $colDetails['type'], $colDetails['method'], $reference,  $colDetails['default']);
			}
			$this->multTNGs[$i-1]->setPrimaryKey($this->primaryKey, $this->primaryKeyColumn['type']);

			$this->multTNGs[$i-1]->compileColumnsValues();

			if ($this->getError()) {
				$this->multTNGs[$i-1]->setError($this->getError());
			}

			$this->multTNGs[$i-1]->setStarted(true);
			$this->multTNGs[$i-1]->doTransaction();
			
			if ($this->multTNGs[$i-1]->getError()) {
				$sw = $this->multTNGs[$i-1]->wereValuesSubmitted();
				if ($sw) {
					$failed = true;
				} else {
					if ($i!=1) {
						// if there was an unival error on one of the 2nd-to-last inserts, ignore it.
						$this->multTNGs[$i-1]->setError(null);
					}
				}
			} else {
				$this->noSuccess++;
				$this->primaryKeyColumn['value'] = $this->multTNGs[$i-1]->getPrimaryKeyValue();
			}
		}
		if ($this->noSuccess == 0) {
			$failed = true;
		}
		if ($failed) {
			$ret = new tNG_error('MINS_ERROR', array(), array());
			if ($this->executeSubSets === false) {
				for ($i=0;$i<sizeof($this->multTNGs);$i++) {
					if (!$this->multTNGs[$i]->getError()) {
						$this->multTNGs[$i]->setError($ret);
						$this->multTNGs[$i]->executeTriggers('ERROR');
					}
				}
			}
		}
		if ($this->executeSubSets === false) {
			$this->noSuccess = 0;
		}
		tNG_log::log('tNG_multipleInsert', 'prepareSQL', 'end');
		return $ret;
	}
	
	/**
	 * Get the local recordset associated to this transaction
	 * @return object resource Recordset resource
	 * @access protected
	 */
	function getLocalRecordset() {
		//Transaction was not started, use the default values
		$fakeArr = array();
		$tmpArr = $this->columns;
		$fakeRs = array();
		if (!isset($tmpArr[$this->primaryKey])) {
			$tmpArr[$this->primaryKey] = $this->primaryKeyColumn;
			$tmpArr[$this->primaryKey]['default'] = NULL;
		}
		foreach($tmpArr as $colName=>$colDetails) {
			$tmpVal = KT_escapeForSql($colDetails['default'], $colDetails['type'], true);
			$fakeArr[$colName] = $tmpVal;
		}
		for ($i=0;$i<$this->insertElements;$i++) {
			$fakeArr[$this->pkName] = "KT_NEW";
			$fakeRs[$i] = $fakeArr;
		}
		return $this->getFakeRecordset($fakeRs);
	}

	/**
	 * Adds a column to the transaction
	 * Calls the parent addColumn method then sets the default value.
	 * @param string $colName The column name
	 * @param string $type The column type (NUMERYC_TYPE, STRING_TYPE, etc)
	 * @param string $method The request method (GET, POST, FILE, COOKIE, SESSION)
	 * @param string $reference The submitted variable name (if method=GET and reference=test, value=$_GET['test'])
	 * @param string $defaultValue The default value for the current column
	 * @access public
	 */
	function addColumn($colName, $type, $method, $reference, $defaultValue = '') {
		parent::addColumn($colName, $type, $method, $reference);
		if ($method == "VALUE") {
			$this->columns[$colName]['default'] = $reference;
		} else {
			$this->columns[$colName]['default'] = $defaultValue;
		}
	}

	/**
	 * No data needs to be saved on insert. 
	 * @param none
	 * @return nothing
	 * @access public
	 */
	function saveData() {
		return;
	}
}
?>