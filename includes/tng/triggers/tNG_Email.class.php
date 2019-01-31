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
 * Send Email trigger class.
 * This is also the base class for tNG_EmailRecordset
 * @access public
 */
class tNG_Email {
	/**
	 * transaction in which the trigger will be executed;
	 * @var string
	 * @access public
	 */	
	var $tNG;
	
	/**
	 * from whom the email is send;
	 * @var string
	 * @access public
	 */		 		
	var $from;
	
	/**
	 * to whom send the email;
	 * @var string
	 * @access public
	 */	
	var $to;	
	
	/**
	 * copy carbon mail;
	 * @var string
	 * @access public
	 */	
	var $cc; 
	
	/**
	 * blind copy carbon mail;
	 * @var string
	 * @access public
	 */	
	var $bcc;
	
	/**
	 * subject of the email;
	 * @var string
	 * @access public
	 */	
	var $subject;
	
	/**
	 * body of the email;
	 * @var string
	 * @access public
	 */	
	var $content;
	
	/**
	 * the file from which the content is read and sent;
	 * @var string
	 * @access public
	 */	
	var $contentFile;
	
	/**
	 * the file from which the content is read and sent if is set setContentFile
	 * or the __FILE__;
	 * @var string
	 * @access protected
	 */	
	var $file;
	
	/**
	 * encoding type;
	 * @var string
	 * @access public
	 */	
	var $encoding;
	
	/**
	 * format of the email: text or html/text;
	 * @var string
	 * @access public
	 */	
	var $format;
	
	/**
	 * importance of the email: Normal, Low, High;
	 * @var string
	 * @access public
	 */	
	var $importance;	
	
	/**
	 * escap method to use in KT_dynamicvalue() default 'none';
	 * @var string
	 * @access privat
	 */	
	var $escapeMethod;
	
	/**
	 * if it must be used the saveData or not;
	 * @var boolean
	 * @access privat
	 */	
	var $useSavedData;	
	
	/**
	 * filename with absolute paths of the attachments;
         * Only for PRO version
	 * @var array
	 * @access private
	 */
	var $attachments = array();
	/**
	 * type of attachement;
	 * @var string
	 * @access public
	 */
	var $type;
	/**
	 * base folder for attachments;
	 * @var string
	 * @access public
	 */
	var $folder;
	/**
	 * recordset name for attachments;
	 * @var string
	 * @access public
	 */
	var $rsName;
	/**
	 * tng error;
	 * @var object
	 * @access public
	 */
	var $error;
	
	/**
	 * Constructor. Set transaction.
	 * @param object $tNG the transaction
	 * @access public
	 */
	function tNG_Email(&$tNG)
	{
		$this->tNG = &$tNG;
		$this->from = '';
		$this->to = '';
		$this->cc = '';
		$this->bcc = '';
		$this->subject = '';
		$this->content = '';
		$this->contentFile = '';
		$this->file = basename(KT_getUri());
		$this->encoding = '';
		$this->priority = 3;
		$this->escapeMethod = 'none';
	}
	
	/**
	 * Setter. Set from.
	 * @param string $from the from string to use in header
	 * @access public
	 */
	function setFrom($from) 
	{
		$this->from = $from;
	}
	
	/**
	 * Setter. Set to.
	 * @param string $to the string from which to retrieve the email to which the email is send;
	 * @access public
	 */
	function setTo($to) 
	{
		$this->to = $to;
	}
	
	/**
	 * Setter. Set cc.
	 * @param string $cc the carbon copy string from which to retrieve the email address
	 * @access public
	 */
	function setCC($cc) 
	{
		$this->cc = $cc;
	}
	
	/**
	 * Setter. Set bcc.
	 * @param string $bcc the blind carbon copy string from which to retrieve the email address
	 * @access public
	 */
	function setBCC($bcc) 
	{
		$this->bcc = $bcc;
	}
	
	/**
	 * Setter. Set subject.
	 * @param string $subject set the subject of the email or the string from which to retrieve the subject
	 * @access public
	 */
	function setSubject($subject) 
	{
		$this->subject = $subject;
	}
	
	/**
	 * Setter. Set content.
	 * @param string $content set the content from which the body of the email will be retrieved.
	 * @access public
	 */
	function setContent($content) 
	{
		$this->content = $content;
	}
	
	/**
	 * Setter. Set contentFile.
	 * @param string $contentFile the file from which the email body is retrieved.
	 * @access public
	 */
	function setContentFile($contentFile) 
	{
		$this->file = $contentFile;
		$this->contentFile = KT_DynamicData($contentFile, $this->getTng(), $this->escapeMethod, $this->getUseSavedData(), array());
	}
	
	/**
	 * Setter. sets the type of the attachement
         * Only for PRO version
	 * @param string type
	 * @access public
	 */
	function addAttachment($type) {
		$this->type = strtolower($type);
	}
	/**
	 * Setter. sets basefolder
         * Only for PRO version
	 * @param string 
	 * @access public
	 */
	function setAttachmentBaseFolder($folder) {
		$this->folder = KT_realpath($folder, true);
	}
	/**
	 * Setter. Sets recordset name
         * Only for PRO version
	 * @param string
	 * @access public
	 */
	function setAttachmentRecordset($rsName) {
		$this->rsName = $rsName;
	}
	/**
	 * Setter. Sets the attachmetns 
         * Only for PRO version
	 * @param string rename rule
	 * @access public
	 */
	function setAttachmentRenameRule($renameRule) {
		if ($this->type == 'recordset' && $this->error == '') {
			if (!isset($GLOBALS[$this->rsName])) {
				$this->error = new tNG_error('EMAIL_ERROR_RECORDSET', array(), array($this->rsName));
				return;
			}
			$recordset = &$GLOBALS[$this->rsName];
			if (is_resource($recordset)) {
				$rs = new KT_Recordset($recordset); 
			} else {
				$rs = &$recordset;
			}
			
			$rs->MoveFirst();
			while (!$rs->EOF) {
				$GLOBALS["row_".$this->rsName] = $rs->fields;
				$renameRule2 = KT_DynamicData($renameRule, null, null, false, array());
				
				// security
				if (substr(KT_realpath($this->folder . $renameRule2, false), 0, strlen($this->folder)) != $this->folder) {
					$this->error = new tNG_error("EMAIL_ERROR_FOLDER", array(), array(KT_realpath($this->folder . $renameRule2, false), $this->folder));		
					break;	
				} else {
					if (is_file($this->folder . $renameRule2)) {
						$this->attachments[] = $this->folder . $renameRule2;
					}
				}	
				$rs->MoveNext();
			}	
			$rs->MoveFirst();		
		} else if ($this->type == 'custom' && $this->error == '') {
			$renameRule = KT_DynamicData($renameRule, $this->getTng(), $this->escapeMethod, $this->getUseSavedData(), array());
			// security
			if (substr(KT_realpath($this->folder . $renameRule, false), 0, strlen($this->folder)) != $this->folder) {
				$this->error = new tNG_error("EMAIL_ERROR_FOLDER", array(), array(KT_realpath($this->folder . $renameRule, false), $this->folder));			
			} else {
				if (is_file($this->folder . $renameRule)) {
					$this->attachments[] = $this->folder . $renameRule;
				}
			}	
		}
		$this->type = '';
		$this->folder = '';
		$this->rsName = '';
	}
	
	/**
	 * Setter. encoding.
	 * @param string $encoding the encoding to use for encoding the email;
	 * @access public
	 */
	function setEncoding($encoding) 
	{
		$this->encoding = $encoding;
	}
	
	/**
	 * Setter. Set format.
	 * @param string $format the format text or html/text of the email
	 * @access public
	 */
	function setFormat($format) 
	{
		$this->format = strtolower($format);
	}
	
	/**
	 * Setter. Set importance.
	 * @param string $importance set the importance of the email: Normal, High or Low;
	 * @access public
	 */
	function setImportance($importance) 
	{
		$this->importance = strtolower($importance);
	}
	
	/**
	 * Getter. Get tNG object.
	 * @return object transaction object
	 * @access public
	 */
	function getTng()
	{
		return $this->tNG;	
	}
	
	/**
	 * Getter. Get From.
	 * @return string
	 * @access public
	 */
	function getFrom()
	{
		return KT_DynamicData($this->from, $this->getTng(), $this->escapeMethod, $this->getUseSavedData(), array('KT_defaultSender'=>$GLOBALS['tNG_email_defaultFrom']));	
	}
	
	/**
	 * Getter. Get to.
	 * @return string
	 * @access public
	 */
	function getTo()
	{
		return KT_DynamicData($this->to, $this->getTng(), $this->escapeMethod, $this->getUseSavedData(), array('KT_defaultSender'=>$GLOBALS['tNG_email_defaultFrom'])); 
	}
	
	/**
	 * Getter. Get cc.
	 * @return string
	 * @access public
	 */
	function getCc()
	{
		return KT_DynamicData($this->cc, $this->getTng(), $this->escapeMethod, $this->getUseSavedData(), array('KT_defaultSender'=>$GLOBALS['tNG_email_defaultFrom'])); 
	}
	
	/**
	 * Getter. Get bcc.
	 * @return string
	 * @access public
	 */
	function getBcc()
	{
		return KT_DynamicData($this->bcc, $this->getTng(), $this->escapeMethod, $this->getUseSavedData(), array('KT_defaultSender'=>$GLOBALS['tNG_email_defaultFrom'])); 
	}
	
	/**
	 * Getter. Get subject.
	 * @return string
	 * @access public
	 */
	function getSubject()
	{
		return KT_DynamicData($this->subject, $this->getTng(), $this->escapeMethod, $this->getUseSavedData(), array('KT_defaultSender'=>$GLOBALS['tNG_email_defaultFrom']));
	}
	
	/**
	 * Getter. Get encoding.
	 * @return string
	 * @access public
	 */
	function getEncoding()
	{
		return $this->encoding;	
	}
	
	/**
	 * Getter. Get textBody.
	 * @return string the text body of the email, stripped by all html tags;
	 * @access protected
	 */
	function getTextBody()
	{	
		$content = KT_DynamicData($this->content, $this->getTng(), $this->escapeMethod, $this->getUseSavedData(), array(), false);
		$content = $this->removeScript($content);
		$content = $this->removeStyle($content);
		return trim(strip_tags($content));
	}
	
	/**
	 * Getter. Get htmlBody.
	 * @return string the content of the email (can have any html tags but <script>)
	 * @access protected
	 */
	function getHtmlBody()
	{
		if ($this->format!='text') {
			$text = KT_DynamicData($this->content, $this->getTng(), $this->escapeMethod, $this->getUseSavedData(), array(), false);
			$text = $this->removeScript($text);
			return KT_transformsPaths(KT_makeIncludedURL($this->file), $text, true);
		} else {
			return ;
		}	
	}
	
	
	/**
	 * Remove the <script></script> or <script > tags from the 
	 * @param string $text the text to be stripped
	 * @return string the stripped text
	 * @access protected
	 */
	function removeScript($text)
	{
		preg_match_all("/<\s*script[^>]*>(.*)<\s*\/\s*script\s*>/ims", $text, $matches);
		if (is_array($matches) && !empty($matches[0])) {
			foreach ($matches[0] as $key => $val) {
				$text = str_replace($val, '', $text);
			}
		}
		preg_match_all("/<\s*script[^>]*>/ims", $text, $matches);
		if (is_array($matches) && !empty($matches[0])) {
			foreach ($matches[0] as $key => $val) {
				$text = str_replace($val, '', $text);
			}
		}
		return $text;
	}
	
	/**
	 * remove the <style> tags from the text;
	 * @param string $text the text to be stripped
	 * @return string the text without <style> tags
	 * @access private
	 */
	function removeStyle($text)
	{
		preg_match_all("/<\s*style[^>]*>(.*)<\s*\/\s*style\s*>/ims", $text, $matches);
		if (is_array($matches) && !empty($matches[0])) {
			foreach ($matches[0] as $key => $val) {
				$text = str_replace($val, '', $text);
			}
		}
		return $text;
	}
	
	/**
	 * Getter. Get useSavedData.
	 * @return boolean true if the transaction was a delete one;
	 * @access protected
	 */
	function getUseSavedData()
	{
		if (!isset($this->useSavedData)) {
			if ($this->tNG->getTransactionType()=='_delete' || $this->tNG->getTransactionType()=='_multipleDelete') {
				$this->useSavedData = true;
			} else {
				$this->useSavedData = false;
			}
		}	
		return $this->useSavedData;	
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
		
		$email = new KT_Email();
		$email->setPriority($this->importance);
		foreach ($this->attachments as $filename) {
			$email->addAttachment($filename);
		}
		$email->sendEmail($GLOBALS['tNG_email_host'], $GLOBALS['tNG_email_port'], $GLOBALS['tNG_email_user'], $GLOBALS['tNG_email_password'], $this->getFrom(), $this->getTo(), $this->getCc(), $this->getBcc(), $this->getSubject(), $this->getEncoding(), $this->getTextBody(), $this->getHtmlBody());
		if ($email->hasError()) {
			$arr = $email->getError();
			return new tNG_error('EMAIL_FAILED', array(''), array($arr[1]));
		} 
	}
			
}

?>