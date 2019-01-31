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
 *  send Email Recordset trigger class.
 * Extend tNG_EmailRecordset
 * @access public
 */
class tNG_EmailRecordset extends tNG_Email {
	
	/**
	 * recordset;
	 * @param object
	 * @access private
	 */	
	var $recordset;
	/**
	 * recordset name;
	 * @param string
	 * @access public
	 */
	var $recordsetName;
	/**
	 * error object;
	 * @param object
	 * @access private
	 */	
	var $error;
	
	/**
	 * Setter. Set recordsetname .
	 * @param string $recordsetName is the name of the recordset
	 * @access public
	 */
	function setRecordset($recordsetName)
	{
		$this->recordsetName = $recordsetName;
		if (!isset($GLOBALS[$recordsetName])) {
			$this->error = new tNG_error('EMAIL_ERROR_RECORDSET', array(), array($recordsetName));
			return;
		}
		$recordset = &$GLOBALS[$recordsetName];
		if (is_resource($recordset)) {
			$this->recordset = new KT_Recordset($recordset); 
		} else {
			$this->recordset = &$recordset;
		}
	}
	
	/**
	 * Getter. Get to.
	 * @return string the address to which the email is send;  
	 * @access public
	 */
	function getTo()
	{
		return $this->recordset->Fields($this->to);
	}
	
	/**
	 * the main method, execute the code of the class;
	 * @access public
	 */
	function Execute() 
	{
		if (is_object($this->error)) {
			return $this->error;
		}
		if ($this->contentFile!='' && file_exists($this->contentFile) && $fp = fopen($this->contentFile, 'r')) {
			$this->content = fread($fp, filesize($this->contentFile));
			fclose($fp);
		} else if ($this->contentFile!='') {
			return new tNG_error('EMAIL_NO_TEMPLATE', array(), array());
		}
		
		$arrErrors = array();
		while (!$this->recordset->EOF) {
			$GLOBALS["row_".$this->recordsetName] = $this->recordset->fields;
			$email = new KT_Email();
			$email->setPriority($this->importance);
			foreach ($this->attachments as $filename) {
				$email->addAttachment($filename);
			}
			$email->sendEmail($GLOBALS['tNG_email_host'], $GLOBALS['tNG_email_port'], $GLOBALS['tNG_email_user'], $GLOBALS['tNG_email_password'], $this->getFrom(), $this->getTo(), $this->getCc(), $this->getBcc(), $this->getSubject(), $this->getEncoding(), $this->getTextBody(), $this->getHtmlBody());
			if ($email->hasError()) {
				$arr = $email->getError();
				$arrErrors[] = 'Email to user: <strong>'.$this->getTo().'</strong> was not sent. Error returned: '.$arr[1]; 
			}
			$this->recordset->MoveNext();
		} 
		if (count($arrErrors)>0) {
			return new tNG_error('EMAIL_FAILED', array(''), array(implode('<br />',$arrErrors)));
		}
	}
		
}

?>