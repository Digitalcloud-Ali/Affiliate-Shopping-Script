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

$DCM_SELECT_SIZE_OPTION_NAME = "size";
$DCM_GLOBALOBJECT = 'CommaMenus';
if (typeof window[$DCM_GLOBALOBJECT] == 'undefined') {
	window[$DCM_GLOBALOBJECT] = {};
}

function MXW_CommaMenu (input) {
	this.painted = false;
	this.name = input;

	var originalElement = document.getElementById(input);
	if (typeof(originalElement.kt_uni_attached) == 'undefined') {
		originalElement.kt_uni_attached = true;
	} else {
		return;
	}

	this.input = originalElement;
	this.input.widget_id = this.name;
	this.input.widget_type = $DCM_GLOBALOBJECT;

	this.recordset = new JSRecordset(WDG_getAttributeNS(this.input, 'recordset'));
	this.valuefield = WDG_getAttributeNS(this.input, 'valuefield');
	this.displayfield = WDG_getAttributeNS(this.input, 'displayfield');
	var ss = parseInt(WDG_getAttributeNS(this.input, $DCM_SELECT_SIZE_OPTION_NAME));
	this.select_size = isNaN(ss)?8:ss;

	window[$DCM_GLOBALOBJECT][input] = this;

	this.inspect();
	this.setEnabled(!this.input.disabled);
	try {Kore.addUnloadListener(this.dispose, this);}catch(err){}
}

MXW_CommaMenu_dispose = function() {
	try {Kore.removeUnloadListener(this.dispose, this);} catch(err) {}
	try{delete window[$DCM_GLOBALOBJECT][this.name];}catch(err){}
}
MXW_CommaMenu.prototype.dispose = MXW_CommaMenu_dispose;

function MXW_CommaMenu_setEnabled(state) {
	this.input.disabled = !state;
	this.menu.disabled = !state;
}
MXW_CommaMenu.prototype.setEnabled = MXW_CommaMenu_setEnabled;

function MXW_CommaMenu_paint(forceRepaint) {
	if (typeof forceRepaint=="undefined") {
		forceRepaint = false;
	}
	if(this.painted && !forceRepaint ) {
		return;
	}
	
	if (forceRepaint && this.menu) {
		this.menu.parentNode.removeChild(this.menu);
	}

	var tmp = utility.dom.createElement("SELECT", {
		"multiple"		: "true",
		"size"		: this.select_size,
		"id"		: this.name +"_select"
	});
	
	this.menu = utility.dom.insertAfter(tmp, this.input);
	WDG_setAttributeNS(this.menu, 'cbFor', this.name);
	if (!is.opera) {
		this.menu.onchange = MXW_CommaMenu_menu_click;
	} else {
		this.menu.onmousedown = MXW_CommaMenu_menu_click;
	}

	this.recordset.MoveFirst();
	while(this.recordset.MoveNext()) {
		var o = new Option(this.recordset.Fields(this.displayfield),this.recordset.Fields(this.valuefield));
		this.menu.options[this.menu.options.length] = o;
	}
	this.input.style.display = "none";

	this.painted = true;
}

function MXW_CommaMenu_inspect() {
	this.paint();
	var strValues = this.input.value;
	var arrValues = strValues.split(/,/g);
	for (var i=0; i<arrValues.length; i++) {
		arrValues[i] = String_trim(arrValues[i]);
	}
	this.menu.selectedIndex = -1;
	var obj = document.getElementById(this.name +"_select");
	
	for (var i=0; i < obj.options.length; i++) {
		if (Array_indexOf(arrValues, obj.options[i].value) != -1) {
			setTimeout('MXW_CommaMenu_lateSelect("'+this.name+'_select", '+i+')', 1);
		}
	}
}

function MXW_CommaMenu_lateSelect(zid, i) {
	document.getElementById(zid).options[i].selected = true;
}

function MXW_CommaMenu_apply() {
	var newValue = "";
	for(var i=0; i<this.menu.options.length; i++) {
		if(this.menu.options[i].selected) {
			newValue += (newValue==""?"":",") + this.menu.options[i].value;
		}
	}
	this.input.value = newValue;
}
function MXW_CommaMenu_menu_click() {
	window[$DCM_GLOBALOBJECT][WDG_getAttributeNS(this, 'cbFor')].apply();
}
MXW_CommaMenu.prototype.paint = MXW_CommaMenu_paint;
MXW_CommaMenu.prototype.apply = MXW_CommaMenu_apply;
MXW_CommaMenu.prototype.inspect = MXW_CommaMenu_inspect;

