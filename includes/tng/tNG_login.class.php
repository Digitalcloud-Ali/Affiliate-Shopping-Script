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
 * This class is the "login transaction" class contructed over the "custom" transaction class.
 * @access public
 */
class tNG_login extends tNG_custom {
	/**
	 * login type; 
	 * @var string loginType
	 * @access public
	 */
	var $loginType = 'form';
	/**
	 * Constructor. Sets the connection, the database name and other default values.
	 * Also sets the transaction type.
	 * @param object KT_Connection &$connection the connection object
	 * @access public
	 */
	function tNG_login(&$connection) {
		parent::tNG_custom($connection);
		$this->transactionType = '_login';
		//TODO: Check that $GLOBALS['tNG_login_config']['table'] really exist. If not, die w/error
		if ($GLOBALS['tNG_login_config']['table'] == "") {
			die("Internal error. Please configure your login table in InterAKT Control Panel > Login Settings.");
		}
		if ($GLOBALS['tNG_login_config']['pk_field'] == "" || $GLOBALS['tNG_login_config']['pk_type'] == "") {
			die("Internal error. Please configure your login primary key in InterAKT Control Panel > Login Settings.");
		}
		
		$this->setPrimaryKey($GLOBALS['tNG_login_config']['pk_field'], $GLOBALS['tNG_login_config']['pk_type']);
		$this->exportRecordset = true;
		$this->registerTrigger("AFTER", "Trigger_Login_CheckLogin", -20);
		if ($GLOBALS['tNG_login_config']['activation_field'] != "") {
			$this->registerTrigger("AFTER", "Trigger_Login_CheckUserActive", -16);
		}
		if (isset($GLOBALS['tNG_login_config']['registration_date_field']) && isset($GLOBALS['tNG_login_config']['expiration_interval_field']) && isset($GLOBALS['tNG_login_config']['expiration_interval_default']) && $GLOBALS['tNG_login_config']['registration_date_field']!='' && $GLOBALS['tNG_login_config']['expiration_interval_default']!='' && $GLOBALS['tNG_login_config']['expiration_interval_field']!='') {
			$this->registerTrigger("AFTER", "Trigger_Login_Account_Expiration", -12);
		}
		$this->registerTrigger("AFTER", "Trigger_Login_AddDynamicFields", -6);
		$this->registerTrigger("AFTER", "Trigger_Login_SaveDataToSession", -4);
		$this->registerTrigger("AFTER", "Trigger_Login_AutoLogin", -2);
		if (isset($GLOBALS['tNG_login_config']['max_tries']) && isset($GLOBALS['tNG_login_config']['max_tries_field']) && isset($GLOBALS['tNG_login_config']['max_tries_disableinterval']) && isset($GLOBALS['tNG_login_config']['max_tries_disabledate_field']) && $GLOBALS['tNG_login_config']['max_tries']!='' && $GLOBALS['tNG_login_config']['max_tries_field']!='' && $GLOBALS['tNG_login_config']['max_tries_disableinterval']!='' && $GLOBALS['tNG_login_config']['max_tries_disabledate_field']!='') {
			$this->registerTrigger("AFTER", "Trigger_Login_MaxTriesCheck", -30);
			$this->registerTrigger("AFTER", "Trigger_Login_MaxTriesReset", 110);
			$this->registerTrigger("ERROR", "Trigger_Login_MaxTriesIncrement", -10);
			
		}
		if ( isset($GLOBALS['tNG_login_config']['logger_table']) && isset($GLOBALS['tNG_login_config']['logger_pk']) && isset($GLOBALS['tNG_login_config']['logger_user_id']) && isset($GLOBALS['tNG_login_config']['logger_ip']) && isset($GLOBALS['tNG_login_config']['logger_datein']) && isset($GLOBALS['tNG_login_config']['logger_datelastactivity']) && isset($GLOBALS['tNG_login_config']['logger_session']) && 
			$GLOBALS['tNG_login_config']['logger_table']!='' && $GLOBALS['tNG_login_config']['logger_pk']!='' &&  $GLOBALS['tNG_login_config']['logger_user_id']!='' && $GLOBALS['tNG_login_config']['logger_ip']!='' && $GLOBALS['tNG_login_config']['logger_datein']!='' && $GLOBALS['tNG_login_config']['logger_datelastactivity']!='' && $GLOBALS['tNG_login_config']['logger_session']!='') {
						
			$this->registerTrigger("AFTER", "Trigger_Login_LoggerIn", 2);
		}
	}
	
	/**
	 * Setter. ste the type of login transaction
	 * @param string $loginType
	 * @access public
	 */
	function setLoginType($loginType) {
		$this->loginType = $loginType;
	}
	
	/**
	 * Prepares the custom SQL query to be executed
	 * @access protected
	 */
	function prepareSQL() {
		tNG_log::log('tNG_login', 'prepareSQL', 'begin');
		
		$table = $GLOBALS['tNG_login_config']['table'];
		$pk_column = $this->getPrimaryKey();
		$user_column = $GLOBALS['tNG_login_config']['user_field'];
		$password_column = $GLOBALS['tNG_login_config']['password_field'];

		$sql = "SELECT ".$table.".*, ". KT_escapeFieldName($pk_column) ." AS kt_login_id, ". KT_escapeFieldName($user_column) ." AS kt_login_user, ". KT_escapeFieldName($password_column)." AS kt_login_password ";
		if (isset($GLOBALS['tNG_login_config']['max_tries']) && isset($GLOBALS['tNG_login_config']['max_tries_field']) && isset($GLOBALS['tNG_login_config']['max_tries_disableinterval']) && isset($GLOBALS['tNG_login_config']['max_tries_disabledate_field']) && $GLOBALS['tNG_login_config']['max_tries']!='' && $GLOBALS['tNG_login_config']['max_tries_field']!='' && $GLOBALS['tNG_login_config']['max_tries_disableinterval']!='' && $GLOBALS['tNG_login_config']['max_tries_disabledate_field']!='') {
			$sql .= ', '.KT_escapeFieldName($GLOBALS['tNG_login_config']['max_tries_field']).' AS kt_login_maxtries, '.KT_escapeFieldName($GLOBALS['tNG_login_config']['max_tries_disabledate_field']).' AS kt_login_maxtriesdate ';
		}
		if (isset($GLOBALS['tNG_login_config']['registration_date_field']) && isset($GLOBALS['tNG_login_config']['expiration_interval_field']) && isset($GLOBALS['tNG_login_config']['expiration_interval_default']) && $GLOBALS['tNG_login_config']['registration_date_field']!='' && $GLOBALS['tNG_login_config']['expiration_interval_default']!='' && $GLOBALS['tNG_login_config']['expiration_interval_field']!='') {
			$sql .= ', '.KT_escapeFieldName($GLOBALS['tNG_login_config']['expiration_interval_field']).' AS kt_login_expiration_interval, '.KT_escapeFieldName($GLOBALS['tNG_login_config']['registration_date_field']).' AS kt_login_regdate ';
		}
		$sql .= " FROM ". $table;
		if ($this->loginType == 'form') {
			$sql.= " WHERE ".KT_escapeFieldName($user_column). "={kt_login_user}";
		} else {
			$sql.= " WHERE ".KT_escapeFieldName($pk_column). "={kt_login_id}";
		}
		
		$sql = KT_DynamicData($sql, $this, "SQL");
		$this->setSQL($sql);
		tNG_log::log('tNG_login', 'prepareSQL', 'end');
		return null;
	}
	
	/**
	 * Get the local recordset associated to this transaction
	 * @return object resource Recordset resource
	 * @access protected
	 */
	function getLocalRecordset() {
		tNG_log::log('tNG_login', 'getLocalRecordset');
		$fakeArr = array();
		$tmpArr = $this->columns;
		foreach($tmpArr as $colName=>$colDetails) {
			$tmpVal = KT_escapeForSql($colDetails['value'], $colDetails['type'], true);
			$fakeArr[$colName] = $tmpVal;
		}
		return $this->getFakeRecordset($fakeArr);
	}

	
}
?>