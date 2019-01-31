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
 * This class is the "logout transaction" class contructed over the "custom" transaction class.
 * Only for PRO version	 
 * @access public
 */
class tNG_logoutTransaction extends tNG_custom {
	
	/**
	 * Constructor. Sets the connection, the database name and register the download trigger.
	 * @param object KT_Connection &$connection the connection object
	 * @access public
	 */
	function tNG_logoutTransaction(&$connection) {
		parent::tNG_custom($connection);
		if ( isset($GLOBALS['tNG_login_config']['logger_table']) && isset($GLOBALS['tNG_login_config']['logger_pk']) && isset($GLOBALS['tNG_login_config']['logger_user_id']) && isset($GLOBALS['tNG_login_config']['logger_ip']) && isset($GLOBALS['tNG_login_config']['logger_datein']) && isset($GLOBALS['tNG_login_config']['logger_datelastactivity']) && isset($GLOBALS['tNG_login_config']['logger_session']) && 
			$GLOBALS['tNG_login_config']['logger_table']!='' && $GLOBALS['tNG_login_config']['logger_pk']!='' &&  $GLOBALS['tNG_login_config']['logger_user_id']!='' && $GLOBALS['tNG_login_config']['logger_ip']!='' && $GLOBALS['tNG_login_config']['logger_datein']!='' && $GLOBALS['tNG_login_config']['logger_datelastactivity']!='' && $GLOBALS['tNG_login_config']['logger_session']!='') {
			
			$this->registerTrigger("AFTER", "Trigger_Login_LoggerOut", 98);
		}	
		$this->addColumn('kt_login_id', 'STRING_TYPE', 'EXPRESSION', '{SESSION.kt_login_id}');	
	}
	
	/**
	 * Executes the Transaction
	 * @access public
	 */
	function executeTransaction() {
		parent::executeTransaction();		
	}		
	
	/**
	 * executing the transaction (triggers, prepare SQL)
	 * @access protected
	 */
	function doTransaction() {
		// destroy login related info
		unset($_SESSION['kt_login_id']);
		KT_unsetSessionVar('kt_login_id');
		unset($_SESSION['kt_login_user']);
		KT_unsetSessionVar('kt_login_user');
		unset($_SESSION['kt_login_level']);
		KT_unsetSessionVar('kt_login_level');
		
		unset($_SESSION['KT_denied_pageuri']);
		KT_unsetSessionVar('KT_denied_pageuri');
		unset($_SESSION['KT_denied_pagelevels']);
		KT_unsetSessionVar('KT_denied_pagelevels');
		// destroy popup/upload/download info
		unset($_SESSION['tng_popup']);
		unset($_SESSION['tng_upload']);
		unset($_SESSION['tng_download']);
		// remove cookies
		$cookie_path = tNG_getRememberMePath();
		setcookie("kt_login_id", "" , time() - 3600, $cookie_path);
		setcookie("kt_login_test", "" , time() - 3600, $cookie_path);
		unset($_COOKIE['kt_login_id']);
		unset($_COOKIE['kt_login_test']);
		if (is_array($GLOBALS['tNG_login_config_session'])) {
			$ses_arr = $GLOBALS['tNG_login_config_session'];
			foreach ($ses_arr as $ses_name => $ses_value) {
				unset($_SESSION[$ses_name]);
				KT_unsetSessionVar($ses_name);
			}
		}
		
		parent::doTransaction();		
	}
	/**
	 * Get the logout for starting the logout transaction
	 * @return string current url on which is added KT_logout_nou=1 
	 * @access public
	 */
	function getLogoutLink() {
		$ret  = KT_getFullURI();
		$ret = KT_addReplaceParam($ret, "KT_logout_now", "1");
		return $ret;
	}
	
}
?>