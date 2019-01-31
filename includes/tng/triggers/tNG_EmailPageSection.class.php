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
 * Send Email Page Section SB class.
 * @access public
 */
class tNG_EmailPageSection extends tNG_Email {
			
	/**
	 * the style which will be applyed;
	 * @var string
	 * @access public
	 */	
	var $css;
	
	/**
	 * Constructor. Set default encoding and default css.
	 * @access public
	 */
	function tNG_EmailPageSection()
	{
		$this->encoding = '';
		$this->css = '';
		$this->tNG = null;
		$this->escapeMethod = 'none';
		$this->useSavedData = false;
	}
	
		
	/**
	 * Getter. Get From.
	 * @return string
	 * @access public
	 */
	function getFrom()
	{
		return KT_DynamicData($this->from, null, 'none', false, array('KT_defaultSender'=>$GLOBALS['tNG_email_defaultFrom']));
	}
	
	/**
	 * Getter. Get to.
	 * @return string
	 * @access public
	 */
	function getTo()
	{
		return KT_DynamicData($this->to, null, 'none', false, array('KT_defaultSender'=>$GLOBALS['tNG_email_defaultFrom']));
	}
	
	/**
	 * Getter. Get cc.
	 * @return string
	 * @access public
	 */
	function getCc()
	{
		return KT_DynamicData($this->cc, null, 'none', false, array('KT_defaultSender'=>$GLOBALS['tNG_email_defaultFrom']));
	}
	
	/**
	 * Getter. Get bcc.
	 * @return string
	 * @access public
	 */
	function getBcc()
	{
		return KT_DynamicData($this->bcc, null, 'none', false, array('KT_defaultSender'=>$GLOBALS['tNG_email_defaultFrom']));
	}
	
	/**
	 * Getter. Get subject.
	 * @return string
	 * @access public
	 */
	function getSubject()
	{
		return KT_DynamicData($this->subject, null, 'none', false, array('KT_defaultSender'=>$GLOBALS['tNG_email_defaultFrom']));
	}
	
	
	/**
	 * Setter. Set file to use for retrieving CSS and encoding.
	 * @param string
	 * @access public
	 */
	function getCSSFrom($file)
	{
		$this->file = $file;
	}
	
	/**
	 * Getter. Get textBody.
	 * @return string the text body of the email, stripped by all html tags;
	 * @access protected
	 */
	function getTextBody()
	{	
		$content = $this->removeScript($this->content);
		$content = $this->removeStyle($content);
		$content = strip_tags($content);
		$content = @html_entity_decode($content, ENT_QUOTES, $this->getEncoding());
		if (strtoupper($this->getEncoding()) == 'UTF-8') {
			$content = @utf8_encode($content);
		}
		return trim($content);
	}
	
	/**
	 * Getter. Get htmlBody.
	 * prepend to the content the html header tags with any CSS if found, and
	 * add the html footer;
	 * @return string the content of the email (can have any html tags but <script>)
	 * @access protected
	 */
	function getHtmlBody()
	{
		if ($this->format!='text') {
			$content = $this->getHeader();
			$content .= $this->content;
			$content .= $this->getFooter();
			return KT_transformsPaths(KT_getUri(), $content, true);
		} else {
			return ;
		}	
	}
	
	/**
	 * Getter. Get html header.
	 * @return string the <html><head>any css or encoding</head><body>
	 * @access protected
	 */
	function getHeader()
	{
		$text = '<html>'."\n".'<header>'."\n";
		$text .= $this->css . "\n";
		$text .= '</header>'."\n".'<body>'."\n";
		return $text;
	}
	
	/**
	 * Getter. Get html footer.
	 * @return string the </body></html>
	 * @access protected
	 */
	function getFooter()
	{
		$text = '</body>'."\n".'</html>'."\n";
		return $text;	
	}
	
	/**
	 * Start the buffering of the output;
	 * @access private
	 */
	function BeginContent()
	{
		ob_start();
	}
	
	/**
	 * Finish the buffering of the output; Save it to content and send it to the browser;
	 * Remove any <script> tags and keep just the content of the <body> tag if exists.
	 * @access public
	 */
	function EndContent()
	{
		$this->content = ob_get_contents();
		ob_end_flush();
		// keep just the body content if we have;
		preg_match("/<body[^>]*>(.*)(<\/body>)?(<\/html>)?/ims", $this->content, $matches);
		if (is_array($matches) && !empty($matches[1])) {
			$this->content = $matches[1];
		}	
		$this->content = $this->removeScript($this->content);
	}
	
	/**
	 * remove the <script> tags from the text;
	 * @param string $text the text to be stripped
	 * @return string the text without <script> tags
	 * @access private
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
	 * the main method, execute the code of the class;
	 * @access public
	 */
	function Execute() 
	{
		$this->searchCss();
		if ($this->encoding=='') {
			$this->encoding = 'iso-8859-1';
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
	
	/**
	 * Parse the content of the $file and keep any CSS found in;
	 * If the encoding is not setted and found one in the text, it will use it.
	 * @access protected
	 */
	function searchCss()
	{
		if ($this->format=='html/text' && $this->file!='' 
				&& file_exists($this->file) 
				&& $fp=fopen($this->file, "r")) {
			$content = fread($fp, filesize($this->file));
			fclose($fp);
			
			// find the css: <link ...>;
			preg_match_all("/<\s*link([^>]*)>/ims", $content, $matches);
			if (isset($matches[1]) && count($matches[1])>0) {
				foreach ($matches[1] as $key=>$val) {
					if (preg_match("/stylesheet/ims", $val)) {
						preg_match("/href\s*=(.*)/ims", $val, $match);
						if (isset($match[1])) {
							$this->css .= '<link rel="stylesheet" type="text/css" href='.$match[1].' />'."\n";
						}
					}
				}
			}
			// find the css: <style>...</style>
			preg_match_all("/<style\s*(type=\"text\/css\")?>([.\s\S]*)<\/style>/ims", $content, $matches);
			if (isset($matches[0]) && count($matches[0])>0) {
				$this->css .= implode("\n", $matches[0]);
			}
			
			// set the encoding
			if ($this->encoding=='') {
				preg_match("/<\s*meta([\s\S][^>]*)>/ims", $content, $matches);
				if (isset($matches[1]) && $matches[1]!='') {
					preg_match("/charset=(.[^>\"\']*)/ims", $content, $matches);
					if (isset($matches[1]) && $matches[1]!='') {
						$this->encoding = $matches[1];
					}
				}
			}
		}	
	}
		
}

?>