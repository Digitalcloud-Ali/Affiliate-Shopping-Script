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
 * handle the show if user log in (with proper levels) behavior;
 * @access public
 */
class tNG_UserLoggedIn {
	/**
	 * possible level values
	 * @var array
	 * @access public
	 */
	var $levels = array();
	/**
	 * connection 
	 * @var object connection
	 * @access public
	 */
	var $connection;
	/**
	 * Constructor. set the connection
	 * @param object connection
	 * @access public
	 */
	function tNG_UserLoggedIn(&$connection) {
		$this->connection = &$connection;
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
	 * Main method of the class. 
	 * If the user is not log in, call tNG_cookieLogin which will try to autologin based on the cookies;
	 * verify if the user is logged in and have the proper levels;
	 * @return boolean true if the user has the rights and is loggedin;
	 * @access public
	 */
	function Execute() {
		tNG_cookieLogin($this->connection);
		// access denied defaults to "redirect_failed" specified in Login Config
		$grantAccess = false;
		
		tNG_clearSessionVars();
		
		if (isset($_SESSION['kt_login_user'])) {
			if (count($this->levels) > 0) {
				if (isset($_SESSION['kt_login_level'])) {
					if (in_array($_SESSION['kt_login_level'], $this->levels) ) {
						$grantAccess = true;
					}
				}
			} else {
				// no levels are required for this page access
				// the user is logged in, so grant the access
				$grantAccess = true;
			}
		}
		return $grantAccess;
	}
}

?>