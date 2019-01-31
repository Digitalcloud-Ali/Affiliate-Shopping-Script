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
 * This class is the "delete" implementation of the tNG_multiple class.
 * @access public
 */
	class tNG_multipleDelete extends tNG_multiple {
		/**
		 * Constructor. Sets the connection, the database name and other default values.
		 * Also sets the transaction type.
		 * @param object KT_Connection &$connection the connection object
		 * @access public
		 */
		function tNG_multipleDelete(&$connection) {
			parent::tNG_multiple($connection);
			$this->transactionType = '_multipleDelete';
		}
		/**
		 * Executes all sub-transactions
		 * @param none
		 * @access protected
		 */
		function prepareSQL() {
			tNG_log::log('tNG_multipleDelete', 'prepareSQL', 'begin');
			$failed = false;
			$ret = null;
			for ($i=1;true;$i++) {
				$tmp = KT_getRealValue("POST", $this->pkName . "_" . $i);
				if (!isset($tmp)) {
					break;
				}
				$this->multTNGs[$i-1] = new tNG_delete($this->connection);
				$this->multTNGs[$i-1]->setDispatcher($this->dispatcher);
				$this->multTNGs[$i-1]->multipleIdx = $i;
				// register triggers
				$this->multTNGs[$i-1]->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "VALUE", true);
				for ($j=0;$j<sizeof($this->multTriggers);$j++) {
					call_user_func_array(array(&$this->multTNGs[$i-1], "registerConditionalTrigger"), $this->multTriggers[$j]);
				}
				// add columns
				$this->multTNGs[$i-1]->setTable($this->table);
				foreach($this->columns as $colName => $colDetails) {
					$this->multTNGs[$i-1]->addColumn($colName, $colDetails['type'], $colDetails['method'], $colDetails['reference'] . "_" . $i);
				}
				$this->multTNGs[$i-1]->setPrimaryKey($this->primaryKey, $this->primaryKeyColumn['type'], "POST", $this->pkName . "_" . $i);

				$this->multTNGs[$i-1]->executeTransaction();
				
				if ($this->multTNGs[$i-1]->getError()) {
					$failed = true;
				}
			}
			if ($failed) {
				$ret = new tNG_error('MDEL_ERROR', array(), array());
			}
			tNG_log::log('tNG_multipleDelete', 'prepareSQL', 'end');
			return $ret;
		}
		/**
		 * No recordset is created when multiple delete is executed; thus the calling of this method set an error;
		 * @param none
		 * @return nothing
		 */ 
		function getLocalRecordset() {
			$this->setError(new tNG_error('MDEL_NO_RS', array(), array()));
		}
	}
?>