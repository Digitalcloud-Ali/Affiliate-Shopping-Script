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
 * the class that handle the logout of the user;
 * @access public
 */
class tNG_Logout {
	/**
	 * logoutType default value: load
	 * @var string
	 * @access public
	 */
	var $logoutType = 'load';
	/**
	 * redirect to page:
	 * @var striung
	 * @access public
	 */
	var $pageRedirect = "";
	/**
	 * setter.
	 * @param string logoutType
	 * @access public
	 */
	function setLogoutType($logoutType = 'load') {
		$this->logoutType = $logoutType;
	}
	/**
	 * setter. set tha page url where the user is redirect after log out
	 * @param string pageRedirect
	 * @access public
	 */
	function setPageRedirect($pageRedirect) {
		$this->pageRedirect = KT_makeIncludedURL($pageRedirect);
	}
	/**
	 * Unset the session variables that have to do with user login;
	 * @return nothing
	 * @access public
	 */
	function unsetAll() {
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
	}
	/**
	 * Main method of the class. Execute the code; Make the redirect
	 * @return nothing
	 * @access public
	 */
	function Execute() {
		// remove sessions
		if (strtolower($this->logoutType) == "load") {
			$this->unsetAll();
			if($this->pageRedirect != "") {
				KT_redir ($this->pageRedirect);
			}	
		}
		else {
			if(isset($_GET['KT_logout_now']) && $_GET['KT_logout_now'] == "true"){
				$this->unsetAll();
				if($this->pageRedirect != "") {
					KT_redir ($this->pageRedirect);
				}else {
					// redirect to self - after removing value for KT_logout_now
					KT_redir (KT_addReplaceParam(KT_getFullUri(), 'KT_logout_now', ''));
				}	
			}
		}	
	}
	/**
	 * getter. get the url for logout
	 * @return string 
	 * @access public
	 */
	function getLogoutLink() {
		return KT_addReplaceParam(KT_getFullUri(), 'KT_logout_now', 'true');
	}
}

?>