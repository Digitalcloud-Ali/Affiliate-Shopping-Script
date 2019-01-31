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
 * This class is the "update" implementation of the tNG_multiple class.
 * @access public
 */
	class tNG_multipleUpdate extends tNG_multiple {
	/**
	 * if is false will check set ther error on each transaction and will reset the Number of transactions executed successfully
	 * @param boolean
	 * @access public
	 */
		var $executeSubSets;
	/**
	 * Constructor. Sets the connection, the database name and other default values.
	 * Also sets the transaction type and the exportRecordset flag to true.
	 * @param object KT_Connection &$connection the connection object
	 * @access public
	 */
		function tNG_multipleUpdate(&$connection) {
			parent::tNG_multiple($connection);
			$this->transactionType = '_multipleUpdate';
			$this->exportRecordset = true;
		}

		/**
		 * Executes all sub-transactions
		 * @access protected
		 */
		function prepareSQL() {
			tNG_log::log('tNG_multipleUpdate', 'prepareSQL', 'begin');
			$ret = null;
			$this->noSuccess = 0;
			$failed = false;
			for ($i=1;true;$i++) {
				$tmp = KT_getRealValue("POST", $this->pkName . "_" . $i);
				if (!isset($tmp)) {
					break;
				}
				$this->multTNGs[$i-1] = new tNG_update($this->connection);
				$this->multTNGs[$i-1]->setDispatcher($this->dispatcher);
				$this->multTNGs[$i-1]->multipleIdx = $i;
				// register triggers
				for ($j=0;$j<sizeof($this->multTriggers);$j++) {
					call_user_func_array(array(&$this->multTNGs[$i-1], "registerConditionalTrigger"), $this->multTriggers[$j]);
				}
				// add columns
				$this->multTNGs[$i-1]->setTable($this->table);
				foreach($this->columns as $colName => $colDetails) {

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
					$this->multTNGs[$i-1]->addColumn($colName, $colDetails['type'], $colDetails['method'], $reference);
				}
				$this->multTNGs[$i-1]->setPrimaryKey($this->primaryKey, $this->primaryKeyColumn['type'], "POST", $this->pkName . "_" . $i);

				$this->multTNGs[$i-1]->compileColumnsValues();

				if ($this->getError()) {
					$this->multTNGs[$i-1]->setError($this->getError());
				}

				$this->multTNGs[$i-1]->setStarted(true);
				$this->multTNGs[$i-1]->doTransaction();
				
				if ($this->multTNGs[$i-1]->getError()) {
					$failed = true;
				} else {
					$this->noSuccess++;
				}
			}
			if ($failed) {
				$ret = new tNG_error('MUPD_ERROR', array(), array());
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

			tNG_log::log('tNG_multipleUpdate', 'prepareSQL', 'end');
			return $ret;
		}
		
		/**
		 * Get the local recordset associated to this transaction
		 * @return object resource Recordset resource
		 * @access protected
		 */
		function getLocalRecordset() {
			tNG_log::log('tNG_multipleUpdate', 'getLocalRecordset');
			$sql = '';
			$tmpArr = $this->columns;
			$tmpArr[$this->primaryKey]['type'] = $this->primaryKeyColumn['type'];
			$tmpArr[$this->primaryKey]['method'] = $this->primaryKeyColumn['method'];
			$tmpArr[$this->primaryKey]['reference'] = $this->primaryKeyColumn['reference'];
			foreach($tmpArr as $colName=>$colDetails) {
				if ($sql != '') {
					$sql .=',';
				}
				$sql.=KT_escapeFieldName($colName);
			}
			$sql .= ', ' . KT_escapeFieldName($this->primaryKey) . ' as ' . KT_escapeFieldName($this->pkName);
			$sql = 'SELECT '.$sql.' FROM '.$this->table;
			
			$tmp_colValue = KT_getRealValue($this->primaryKeyColumn['method'], $this->primaryKeyColumn['reference'] . "_1");
			$pkv = KT_getRealValue($this->primaryKeyColumn['method'], $this->primaryKeyColumn['reference']);
			if (isset($tmp_colValue)) {
				$sql = $sql . ' WHERE ' . KT_escapeFieldName($this->primaryKey) . ' IN (';
				$sql = $sql . KT_escapeForSql($pkv, $this->primaryKeyColumn['type']);
				$cnt = 1;
				while (true) {
					$tmp_colValue = KT_getRealValue($this->primaryKeyColumn['method'], $this->primaryKeyColumn['reference'] . "_" . $cnt++);
					if (isset($tmp_colValue)) {
						$sql = $sql . ", " . KT_escapeForSql($tmp_colValue, $this->primaryKeyColumn['type']);
					} else {
						break;
					}
				}
				$sql = $sql . ')';
			} else {
				$sql = $sql . ' WHERE ' . KT_escapeFieldName($this->primaryKey) . '=';
				$sql = $sql . KT_escapeForSql($pkv, $this->primaryKeyColumn['type']);
			}
			
			$rs = false;
			if (isset($_SESSION['KT_lastUsedList']) && isset($_SESSION['sorter_tso_'.$_SESSION['KT_lastUsedList']])) {
				$tmp_sql = $sql . ' ORDER BY ' . $_SESSION['sorter_tso_'.$_SESSION['KT_lastUsedList']];
				$table_columns = array();
				if (isset($this->connection->servermodel)) {
					$res = $this->connection->Execute('SELECT * FROM ' . $this->table . ' LIMIT 1');
					$table_columns = array_keys($res->fields);
				} else {
					$res = $this->connection->MetaColumns($this->table);
					foreach($res as $field => $col) {
						$table_columns[] = $col->name;
					}
				}
				$order_column = str_replace(' DESC', '', $_SESSION['sorter_tso_'.$_SESSION['KT_lastUsedList']]);
				$order_column = explode('.', $order_column);
				$order_column = $order_column[count($order_column) - 1];
				if (in_array($order_column, $table_columns)) {
					if (isset($this->connection->servermodel)) {
						$rs = $this->connection->MySQL_Execute($tmp_sql);
					} else {
						$rs = $this->connection->Execute($tmp_sql);
					}
				}
			}
			if (!$rs) {
				if (isset($this->connection->servermodel)) {
					$rs = $this->connection->MySQL_Execute($sql);
				} else {
					$rs = $this->connection->Execute($sql);
				}
			}
			if (!$rs) {
				tNG_log::log('KT_ERROR');
				$this->setError(new tNG_error('MUPD_RS', array(), array($this->connection->ErrorMsg(), $sql)));
				echo $this->dispatcher->getErrorMsg();
				exit;
			}
			return $rs;
		}
	}
?>