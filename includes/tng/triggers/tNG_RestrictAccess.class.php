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
* class to handle Restrict access to page
* @access public
*/
class tNG_RestrictAccess {
	/**
	 * relativ path from the site root
	 * @var string
	 * @access public
	 */
	var $relPath = "";
	/**
	 * possible level values
	 * @var array
	 * @access public
	 */
	var $levels = array();
	/**
	 * connection reference
	 * @var object
	 * @access public
	 */
	var $connection;
	
	/**
	 * Constructor. set the connection/relative path 
	 * @param object connection
	 * @param string relative path
	 * @access public
	 */
	function tNG_RestrictAccess(&$connection, $relPath)  {
		$this->connection = &$connection;
		$this->relPath = KT_makeIncludedURL($relPath);
	}
	/**
	 * setter. add to levels array an entry
	 * @param string 
	 * @access public
	 */
	function addLevel($level) {
		array_push($this->levels, $level);
	}
	/**
	 * Main method of the class. grant the access to the page or make the redirect page setted in control panel
	 * @return mix null or error object
	 * @access public
	 */
	function Execute() {
		tNG_cookieLogin($this->connection);

		// access denied defaults to "redirect_failed" specified in Login Config
		$grantAccess = false;
		$redirect_page = $GLOBALS['tNG_login_config']['redirect_failed'];
		
		tNG_clearSessionVars();
		
		if (isset($_SESSION['kt_login_user'])) {
			if (count($this->levels) > 0) {
				if (isset($_SESSION['kt_login_level'])) {
					if (in_array($_SESSION['kt_login_level'], $this->levels) ) {
						$grantAccess = true;
					} else {
						// acceess denied. check for level default redirect pages
						if (is_array($GLOBALS['tNG_login_config_redirect_failed']) && array_key_exists($_SESSION['kt_login_level'], $GLOBALS['tNG_login_config_redirect_failed']) AND $GLOBALS['tNG_login_config_redirect_failed'][$_SESSION['kt_login_level']] != "") {
							$redirect_page = $GLOBALS['tNG_login_config_redirect_failed'][$_SESSION['kt_login_level']];
						} else {
							// the failure page for the current user level is not defined.. so fall back to default
							$redirect_page = $GLOBALS['tNG_login_config']['redirect_failed'];
						}
					}	
				} // if levels are required, and the current user doesn't have one.. access is denied
			} else {
				// no levels are required for this page access
				// the user is logged in, so grant the access
				$grantAccess = true;
			}
		}
		if (!$grantAccess) {
			// save the accessed page into a session for later use
			$_SESSION['KT_denied_pageuri'] = KT_getFullUri();
			KT_setSessionVar('KT_denied_pageuri');
			$_SESSION['KT_denied_pagelevels'] = $this->levels;
			KT_setSessionVar('KT_denied_pagelevels');
			if (isset($_SESSION['KT_max_tries_error'])) {
				$redirect_page = KT_addReplaceParam($redirect_page, 'info', 'MAXTRIES');
			} else if (isset($_SESSION['KT_account_expire_error'])) {
				$redirect_page = KT_addReplaceParam($redirect_page, 'info', 'ACCOUNT_EXPIRE');
			} else {
			$redirect_page = KT_addReplaceParam($redirect_page, 'info', 'DENIED');
			}
			KT_redir ($this->relPath. $redirect_page);
		} else {
			// clear the sessions used for redirect ??
		}
	}
}

?>