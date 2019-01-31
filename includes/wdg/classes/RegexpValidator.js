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

// Copyright 2001-2005 Interakt Online. All rights reserved.

$RVA_ATTRNAME_TYPE = 'kt_type';
$RVA_ATTRNAME_SUBTYPE = 'kt_subtype';

$RVA_DEFAULT_REGEXP = '.*';
$RVA_DEFAULT_ERROR = 'field not valid';
$RVA_MAIN_CLASSNAME = 'RegexpValidator';
$RVA_ERROR_CLASSNAME = 'RegexpValidatorError';

$RVA_GLOBALOBJECT = "RegexpValidators";

// some utility functions

function RVA_univalLoad(e) {
	if (!e) e = window.event;
	if (e.target) targ = e.target;
	else if (e.srcElement) targ = e.srcElement;
	var objs = document.getElementsByTagName('input');
	if (typeof window[$RVA_GLOBALOBJECT] == "undefined") {
		window[$RVA_GLOBALOBJECT] = {
			'objects': [], 
			'active': null
		}
	}
	for ( var i = 0; i < objs.length; i++) {
		var tmp = [];
		if (WDG_getAttributeNS(objs[i], $RVA_ATTRNAME_TYPE) != null && WDG_getAttributeNS(objs[i], $RVA_ATTRNAME_SUBTYPE) != null) {
			if (WDG_getAttributeNS(objs[i], $RVA_ATTRNAME_TYPE).toLowerCase() == 'widget' && WDG_getAttributeNS(objs[i], $RVA_ATTRNAME_SUBTYPE).toLowerCase() == 'regexpval') {
				tmp[tmp.length] = new RegexpValidator(objs[i]);
			}
		}
	}
}

function RegexpValidator(el) {
	this.input = el;
	
	var tmpel = el;
	while (tmpel.tagName.toLowerCase() != 'form' && tmpel.tagName.toLowerCase() != 'body')
		tmpel = tmpel.parentNode;
	this.form = tmpel;
	
	// elements

	this.errorstr = WDG_getAttributeNS(this.input, 'error') || $RVA_DEFAULT_ERROR;
	this.regexp = WDG_getAttributeNS(this.input, 'regexp') || $RVA_DEFAULT_REGEXP;
	

	this.initialize();
	this.render();
	Array_push(window[$RVA_GLOBALOBJECT]['objects'], this);
}

RegexpValidator.prototype.initialize = function() {
}

RegexpValidator.prototype.render = function() {
	this.input.className = $RVA_MAIN_CLASSNAME;

	UNI_attachEvent(this.input, 'blur', checkRegexp, 1);
}


RegexpValidator.prototype.doError = function() {
	oldclass = this.input.className;
	this.input.className = $RVA_ERROR_CLASSNAME;
	alert(this.errorstr);
	this.input.className = oldclass;
	this.input.focus();
}

function checkRegexp(e) {
	o = DOM_setEventVars(e);
	var el = o.targ;
	var jso = find_object_for_input(o.targ);
	var re = new RegExp('^' + jso.regexp + '$', 'i');
	if (!re.exec(el.value)) {
		jso.doError();
		DOM_cancelEvent(e);
		return false;
	}
	
}


function find_object_for_input(el) { //ALWAYS PASS THE INPUT!!!!!!!!!!!!!
	var toret = null;
	for ( var i = 0; i < window[$RVA_GLOBALOBJECT]['objects'].length; i++) {
		if (window[$RVA_GLOBALOBJECT]['objects'][i].input == el) {
			toret = window[$RVA_GLOBALOBJECT]['objects'][i];
			break;
		}
	}
	return toret;
}

