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
 * class that handle the order behavior for a table (a column that is used exclusively to order the table records);
 * @access public
 */
	class tNG_SetOrderField {
		/**
		 * transaction
		 * @var object tNG
		 * @access public
		 */
		var $tNG;
		/**
		 * table name
		 * @var string
		 * @access public
		 */
		var $table;
		/**
		 * order field name
		 * @var string
		 * @access public
		 */
		var $field;
		/**
		 * if the record will inserted first or last from the point of view of order 
		 * @var string
		 * @access public
		 */
		var $mode;
		/**
		 * Constructor. set transaction and default values for other fields
		 * @param object tNG transaction
		 * @access public
		 */
		function tNG_SetOrderField(&$tNG) {
			$this->tNG = &$tNG;
			$this->table = $tNG->getTable();
			$this->field = 'myfield';
			$this->type = 'NUMERIC_TYPE';
			$this->mode = 'LAST';
			if (isset($_GET['kt_insert_first'])) {
				$this->mode = 'FIRST';
			}
		}
		/**
		 * setter. set table name
		 * @param string
		 * @access public
		 */
		function setTable($table) {
			$this->table = $table;
		}
		/**
		 * setter. set field name
		 * @param string
		 * @access public
		 */
		function setFieldName($field) {
			$this->field = $field;
		}
		/**
		 * Main method of the class. Update the value of the order column;
		 * @return mix null or error object
		 * @access public
		 */
		function Execute() {
			$sql = 'SELECT MAX(' . KT_escapeFieldName($this->field) . ') + 1 AS kt_sortvalue FROM ' . $this->table;
			if ($this->mode == 'FIRST') {
				$sql = 'SELECT MIN(' . KT_escapeFieldName($this->field) . ') AS kt_sortvalue FROM ' . $this->table;
			}
			$ret = $this->tNG->connection->Execute($sql);
			if ($ret === false) {
				return new tNG_error('SET_ORDER_FIELD_SQL_ERROR', array(), array($this->tNG->connection->ErrorMsg(), $sql));
			}
			$value = $ret->Fields('kt_sortvalue');
			if ($value == '' || !is_numeric($value)) {
				$value = 1;
			}
			if ($this->mode == 'FIRST') {
				if ($value < 2) {
					$sql = 'UPDATE ' . $this->table . ' SET ' . KT_escapeFieldName($this->field) . ' = ' . KT_escapeFieldName($this->field) . ' + 1';
					$ret = $this->tNG->connection->Execute($sql);
					if ($ret === false) {
						return new tNG_error('SET_ORDER_FIELD_SQL_ERROR', array(), array($this->tNG->connection->ErrorMsg(), $sql));
					}
				} else {
					$value = $value - 1;
				}
			}
			$this->tNG->addColumn($this->field, 'NUMERIC_TYPE', 'VALUE', $value);
			return null;
		}
	}
?>