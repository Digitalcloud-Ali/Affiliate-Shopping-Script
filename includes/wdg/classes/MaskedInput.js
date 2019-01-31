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

$MIN_DIVPREFIX = 'explanation_div_';
$MIN_GLOBALOBJECT = "MaskedInputs";

$MIN_VISUAL_ALERT_DIV_CLASSNAME = 'MXW_MIN_visual_alert_div';
$MIN_VISUAL_ALERT_INPUT_CLASSNAME = 'MXW_MIN_visual_alert_input';

if (typeof window[$MIN_GLOBALOBJECT] == 'undefined') {
	window[$MIN_GLOBALOBJECT] = {};
}


function MXW_MaskedInput(input) {
	var originalElement = document.getElementById(input);
	if (typeof(originalElement.kt_uni_attached) == 'undefined') {
		originalElement.kt_uni_attached = true;
	} else {
		return;
	}

	this.input = originalElement;

	this.savedCSSStyle = this.input.style.cssText;
	if(this.savedCSSStyle == "{}") {
		//IEMac5.2 bug
		this.savedCSSStyle = "";
	}

	this.name = input;

	this.input.disableAutocomplete = true;
	this.input.setAttribute('disableAutocomplete', true);
	this.input.autocomplete = "off";
	this.input.setAttribute('autocomplete', 'off');

	this.strikes = 0;
	this.mask = WDG_getAttributeNS(this.input, 'mask');
	this.restricttomask = (WDG_getAttributeNS(this.input, 'restricttomask')+'').toLowerCase()=="yes";

	this.maskDirty = false;

	var div = utility.dom.createElement('div', {
		'id': $MIN_DIVPREFIX + input, 
		'style': 'position: absolute; display: none; ', 
		'className': $MIN_VISUAL_ALERT_DIV_CLASSNAME
	});
	div.innerHTML = WDG_Messages["the_mask_is"] + '&nbsp;' + this.mask;
	this.div = document.body.appendChild(div);
	this.div.style.display = 'none';
	this.div.style.position = 'absolute';

	var obj = this;

	utility.dom.attachEvent(obj.input, "keyup", function (e){return MXW_MaskedInput_keyuphandler(input, e);}, 1, false, false);
	utility.dom.attachEvent(obj.input, is.mozilla?"keypress":"keydown", function (e){return MXW_MaskedInput_keypresshandler(input, e);}, 1, false, false);
	utility.dom.attachEvent(obj.input, "blur", function (e){
		var toret = MXW_MaskedInput_blurhandler(input, e);
		if (!toret) {
			obj.input.select();
			obj.input.focus();
			utility.dom.stopEvent(e);
		}
	}, 1, false, false);
	utility.dom.attachEvent(obj.input, "focus", function (e){return MXW_MaskedInput_keyuphandler(input, e);}, 1, false, false);
	window[$MIN_GLOBALOBJECT][input] = this;
	try {Kore.addUnloadListener(this.dispose, this);}catch(err){}
}

MXW_MaskedInput_dispose = function() {
	try {Kore.removeUnloadListener(this.dispose, this);} catch(err) {}
	try {this.div.parentNode.removeChild(this.div);}catch(err) {}
	try{delete window[$MIN_GLOBALOBJECT][this.name];}catch(err){}
}

MXW_MaskedInput.prototype.dispose = MXW_MaskedInput_dispose;

function MXW_MaskedInput_formhandler(input, evt) {
	var toret = true;
	var obj = window[$MIN_GLOBALOBJECT][input];
	obj.kt_focused = false;

	var re_full = new RegExp('^' + toregexp(obj.mask) + '$');
	var re_piece = new RegExp('^' + toregexp(obj.mask.substring(0, obj.input.value.length)) + '$');

	if (obj.restricttomask) {
		//validate with ENTIRE MASK
		if (! (obj.input.value.match(re_full) || obj.input.value == '')) { 
			obj.strikes = 3;
			MXW_visualAlert(obj, 1, 'MIN');
			//also give it focus and DELETE 
			/*
			if (!obj.input.value.match(re_piece)) {
				obj.input.value = '';
			}
			*/
			try {
				obj.input.focus();
			} catch(e) { }
			toret = false;
		}
	} else {
		//validate with piece
		if (!obj.input.value.match(re_piece)) { 
			obj.strikes = 3;
			MXW_visualAlert(obj, 1, 'MIN');
			try {
				obj.input.focus();
			} catch(e) { }
			toret = false;
		}
	}

	if (obj.maskDirty) {
		if (obj.input.fireEvent) {
			obj.input.fireEvent("onchange");
		} else if(document.createEvent){
			var me = document.createEvent("Events");
			me.initEvent('change', 0, 0);
			obj.input.dispatchEvent(me);
		}
	}
	return toret;
}

function MXW_MaskedInput_blurhandler(input, evt) {
	var obj = window[$MIN_GLOBALOBJECT][input];
	MXW_visualAlert(obj, 0, 'MIN');

	var toret = true;
	obj.kt_focused = false;

	if (obj.maskDirty) {
		if (obj.input.fireEvent) {
			obj.input.fireEvent("onchange");
		} else if(document.createEvent){
			var me = document.createEvent("Events");
			me.initEvent('change', 0, 0);
			obj.input.dispatchEvent(me);
		}
	}
	return toret;
}

function MXW_MaskedInput_keyuphandler(input, evt) {
	var obj = window[$MIN_GLOBALOBJECT][input];
	if (evt.type!="focus") {
		if (!obj.kt_focused) {
			utility.dom.stopEvent(evt);
			return false;
		}
	} else {
		obj.kt_focused = true;
	}

	var mask = obj.mask;
	function getFirstMatch(value, mask) {
		var size = value.length;
		if(size == 0) {
			return "";
		}
		var rgx = toregexp(mask.substr(0, size));
		var re = new RegExp('^' + rgx + '$');
		
		if (!value.match(re)) { 
			//try without the last character
			return getFirstMatch(value.substr(0, size-1), mask);
		} else {
			return value;
		}

	}
	var tmVal = getFirstMatch(obj.input.value, mask);
	if (obj.input.value != tmVal) {
		try {obj.input.value = tmVal; } catch(e) { obj.input.setAttribute('value', tmVal); } 
		obj.strikes++;
	}
	if(evt.keyCode != 8 && obj.input.value.length != 0) { // backspace and tab
		obj.completeMask();
	}
	if (obj.strikes == 3) {
		MXW_visualAlert(obj, 1, 'MIN');
	}
	if (mask.length == obj.input.value.length) {
		MXW_visualAlert(obj, 0, 'MIN');
		obj.strikes = 0;
	}
	return true;
}

function MXW_MaskedInput_keypresshandler(input, evt) {
	var obj = window[$MIN_GLOBALOBJECT][input];
	if (!obj.kt_focused) {
		utility.dom.stopEvent(evt);
		return false;
	}
	var mask = obj.mask;

	var keyCode = evt.keyCode;
	if (keyCode == 0) {
		keyCode = evt.charCode;
	}
	if (obj.input.value.length == 0 && keyCode != 8 && keyCode != 0 && keyCode!= 9) {
		obj.completeMask();
	}
}

function toregexp(txt) {
	txt = txt.replace(/([-\/\[\]()\*\+\\])/g, '\\$1');
	txt = txt.replace(/9/g, '\\d');
	txt = txt.replace(/\?/g, '.');
	//alphanumeric characters
	//txt = txt.replace(/X/g, '\\w');
	txt = txt.replace(/X/g, '[0-9\u0040-\u005A\u0061-\u007A\u0100-\u017E\u0180-\u0233\u0391-\u03CE\u0410-\u044F\u05D0-\u05EA\u0621-\u063A\u0641-\u064A\u0661-\u06D3\u06F1-\u06FE]');

	//alphabetic characters
	//txt = txt.replace(/A/g, '[A-Za-z]');

	//UNICODE alphabetic characters:
	// Basic Latin - http://www.fileformat.info/info/unicode/block/basic_latin/utf8test.htm
	// Latin Extented A - http://www.fileformat.info/info/unicode/block/latin_extended_a/utf8test.htm
	// Latin Extended-B - http://www.fileformat.info/info/unicode/block/latin_extended_b/utf8test.htm
	// Greek - http://www.fileformat.info/info/unicode/block/greek_and_coptic/utf8test.htm
	// Cyrillic - http://www.fileformat.info/info/unicode/block/cyrillic/utf8test.htm
	// Hebrew - http://www.fileformat.info/info/unicode/block/hebrew/utf8test.htm
	// Arabic - http://www.fileformat.info/info/unicode/block/arabic/utf8test.htm
	txt = txt.replace(/A/g, '[\u0041-\u005A\u0061-\u007A\u0100-\u017E\u0180-\u0233\u0391-\u03CE\u0410-\u044F\u05D0-\u05EA\u0621-\u063A\u0641-\u064A\u0661-\u06D3\u06F1-\u06FE]');
	return txt;
}

MXW_MaskedInput.prototype.completeMask = function() {
	var size = this.input.value.length;
	var mask = this.mask;
	var sw=true;
	var tmp = this.input.value;

	while (sw) {
		if (mask.length <= size) {
			break;
		}
		switch (mask.charAt(size)) {
			case '9':
			case 'X':
			case 'A':
			case '?':
				sw = false;
				break;
			default:
				tmp += mask.charAt(size);
		}
		size++;
	}
	if (this.input.value != tmp) {
		this.maskDirty = true;
		this.input.value = tmp;
		if (is.opera) {
			MXW_setSelectionRange(this.input, tmp.length+2, tmp.length+2);
		}
		this.lastMatched = this.input.value;
	} else {
		this.maskDirty = false;
	}
	return;
}

