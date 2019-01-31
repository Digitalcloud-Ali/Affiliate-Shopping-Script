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

$RTE_ATTRNAME_MAXCHARS = 'maxchars';
$RTE_ATTRDEFVAL_MAXCHARS = 16384; //16k

$RTE_DIVPREFIX = 'rte_explanation_div_';

$RTE_VISUAL_ALERT_INPUT_CLASSNAME = 'MXW_RTE_visual_alert_input';
$RTE_VISUAL_ALERT_DIV_CLASSNAME = 'MXW_RTE_visual_alert_div'

$RTE_VISUAL_ALERT_ON_CLASSNAME = 'MXW_RTE_visual_alert_on';
$RTE_VISUAL_ALERT_OFF_CLASSNAME = 'MXW_RTE_visual_alert_off';

$RTE_GLOBALOBJECT = "RestrictedTextAreas";
if (typeof window[$RTE_GLOBALOBJECT] == 'undefined') {
	window[$RTE_GLOBALOBJECT] = {};
}
// some utility functions
function MXW_RestrictedTextArea(input) {
	var originalElement = document.getElementById(input);
	if (typeof(originalElement.kt_uni_attached) == 'undefined') {
		originalElement.kt_uni_attached = true;
	} else {
		return;
	}

	this.name = input;
	this.input = originalElement;
	this.input.widget_id = this.name;
	this.input.widget_type = $RTE_GLOBALOBJECT;

	var maxChars = parseInt(WDG_getAttributeNS(this.input, $RTE_ATTRNAME_MAXCHARS));
	this.maxChars = isNaN(maxChars) ? $RTE_ATTRDEFVAL_MAXCHARS : maxChars;

	this.showcount = /true|yes/i.test( (WDG_getAttributeNS(this.input, 'showcount')+'').toLowerCase());
	this.showmessage=/true|yes/i.test( (WDG_getAttributeNS(this.input, 'showmessage')+'').toLowerCase());
	this.message = WDG_Messages["rte_maximum_reached"];

	this.container = utility.dom.createElement('SPAN', {
	});
	this.container = utility.dom.insertAfter(this.container, this.input);
	this.container.innerHTML = '<table class="MXW_Spin_table" border="0" cellpadding="0" cellspacing="0"><tr><td class="MXW_Spin_table_td"></td><td class="MXW_Spin_table_td"><div class="MXW_Spin"></div></td></tr></table>';

	this.input.style.margin="-1px 0px";
	this.container.firstChild.rows[0].firstChild.appendChild(this.input);
	
	var div = utility.dom.createElement('div', {
		'id': $RTE_DIVPREFIX + input, 
		'style': 'position: absolute; display: none; ', 
		'className': $RTE_VISUAL_ALERT_DIV_CLASSNAME
	});
	div.innerHTML = (this.message == '') ? WDG_Messages['max_character_number'] : this.message;
	this.div = document.body.appendChild(div);
	this.div.style.display = 'none';
	this.div.style.position = 'absolute';

	window[$RTE_GLOBALOBJECT][input] = this;

	this.render();
	this.initialize();
	this.updateCharsLeft();
	this.setEnabled(!this.input.disabled);
	try {Kore.addUnloadListener(this.dispose, this);}catch(err){}
	return this;
}

MXW_RestrictedTextArea_dispose = function() {
	try {Kore.removeUnloadListener(this.dispose, this);} catch(err) {}
	try {this.div.parentNode.removeChild(this.div);}catch(err) {}
	try{delete window[$RTE_GLOBALOBJECT][this.name];}catch(err){}
}
MXW_RestrictedTextArea.prototype.dispose = MXW_RestrictedTextArea_dispose;

function MXW_RestrictedTextArea_setEnabled(state) {
	this.input.disabled = !state;
	if (this.showcount) {
		this.charsleft.disabled = !state;
	}
}
MXW_RestrictedTextArea.prototype.setEnabled = MXW_RestrictedTextArea_setEnabled;

MXW_RestrictedTextArea.prototype.numChars = function() {
	return this.input.value.length;
}

MXW_RestrictedTextArea.prototype.initialize = function() {
	var obj = this;
	var input = this.name;
	utility.dom.attachEvent(obj.input, "keyup", function(e){MXW_RestrictedTextArea_keyuphandler(input, e);});
	utility.dom.attachEvent(obj.input, is.mozilla?"keypress":"keydown", function (e){
		return MXW_RestrictedTextArea_keydownhandler(input, e);}, 1
	);
}

function MXW_RestrictedTextArea_keydownhandler(input, e) {
	var o = utility.dom.setEventVars(e);
	var obj = window[$RTE_GLOBALOBJECT][input];
	if (obj.numChars() > obj.maxChars) {
		switch(o.kc){
		case di_UP: case di_DOWN: case di_LEFT: case di_RIGHT:
		case di_PgUP: case di_PgDOWN: case di_HOME: case di_END:
		case di_DELETE: case di_BACKSPACE: case di_TAB: case di_ESC:
			break;
		default:
			utility.dom.stopEvent(o.e);
		}
	}
}
function MXW_RestrictedTextArea_keyuphandler(input, e) {
	var obj = window[$RTE_GLOBALOBJECT][input];
	var o = utility.dom.setEventVars(e);
	if (!o.targ) {
		return;
	}
	var val = obj.input.value;
	var moveCursor = false;

	if (val == obj.oldText) {
		// if the field content did not change since the last update, do nothing
		return;
	}

	if (obj.numChars() > obj.maxChars) {
		// strip trailing characters if text length is bigger than mask length
		val = val.substr(0, obj.maxChars);
		moveCursor = true;
	}

	if (obj.input.value != val) {
		obj.input.value = val; //last calculated correct value
		obj.oldText = val; //update so we can check on next character
	}
	obj.visualAlert();
	obj.updateCharsLeft();

	if (moveCursor) {
	}
}

MXW_RestrictedTextArea.prototype.render = function() {
	if (this.showcount) {
		this.charsleft = utility.dom.createElement("INPUT", {
			"type"	: "text",
			"name"	: "mxw_" + this.name + "_charsleft",
			"id"		: "mxw_" + this.name + "_charsleft",
			"autocomplete":"off",
			"disableAutocomplete":true,
			"size"	: 2,
			"readOnly" : true,
			"tabIndex" : -1,
			"className":$RTE_VISUAL_ALERT_OFF_CLASSNAME
		});
		this.container.firstChild.rows[0].lastChild.appendChild(this.charsleft);
		/*
		this.charsleft = utility.dom.insertAfter(this.charsleft, this.input);
		this.charsleft.style.position = 'absolute';
		var box = utility.dom.getAbsolutePos(this.input);
		this.charsleft.style.left = (box.x + this.input.offsetWidth) + 'px';
		this.charsleft.style.top = (box.y + this.input.offsetHeight - this.charsleft.offsetHeight) + 'px';
		*/
	}
}

MXW_RestrictedTextArea.prototype.visualAlert = function() {
	if (this.showcount && this.charsleft) {
		if (this.numChars() >= this.maxChars) {
			utility.dom.classNameRemove(this.charsleft, $RTE_VISUAL_ALERT_OFF_CLASSNAME);
			utility.dom.classNameAdd(this.charsleft, $RTE_VISUAL_ALERT_ON_CLASSNAME);
		} else {
			utility.dom.classNameRemove(this.charsleft, $RTE_VISUAL_ALERT_ON_CLASSNAME);
			utility.dom.classNameAdd(this.charsleft, $RTE_VISUAL_ALERT_OFF_CLASSNAME);
		}
	}
	if (this.showmessage && ( this.numChars() >= this.maxChars )) {
		//alert(this.message);
		if (this.charsleft) {
			var box = utility.dom.getAbsolutePos(this.charsleft);
			MXW_visualAlert(this, 1, 'RTE', box.x + this.charsleft.offsetWidth - 20, box.y);
		} else {
			var box = utility.dom.getAbsolutePos(this.input);
			MXW_visualAlert(this, 1, 'RTE', box.x + this.input.offsetWidth - 20, box.y);
		}
	} else {
		MXW_visualAlert(this, 0, 'RTE');
	}
}

MXW_RestrictedTextArea.prototype.updateCharsLeft = function() {
	if (this.showcount && this.charsleft) {
		if (this.numChars() > this.maxChars) {
			var val = 0;
		} else {
			var val = this.maxChars - this.numChars();
		}
		this.charsleft.value = val;
	}
}

function isPaste(o) {
	if (String.fromCharCode(o.e.keyCode).toLowerCase() == 'v' && o.e.ctrlKey == true) {
		return true;
	}
	return false;	
}

