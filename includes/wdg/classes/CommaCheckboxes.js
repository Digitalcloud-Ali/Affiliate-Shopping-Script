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

$DCC_GLOBALOBJECT = 'CommaCheckboxes';
if (typeof window[$DCC_GLOBALOBJECT] == 'undefined') {
	window[$DCC_GLOBALOBJECT] = {};
}

function MXW_CommaCheckboxes (input) {
	if (is.safari && is.version < 1.4) {
		return;
	}

	var originalElement = document.getElementById(input);
	if (typeof(originalElement.kt_uni_attached) == 'undefined') {
		originalElement.kt_uni_attached = true;
	} else {
		return;
	}

	this.painted = false;
	this.name = input;
	this.input = originalElement;
	this.input.widget_id = this.name;
	this.input.widget_type = $DCC_GLOBALOBJECT;

	this.recordset = new JSRecordset(WDG_getAttributeNS(this.input, 'recordset'));
	this.valuefield = WDG_getAttributeNS(this.input, 'valuefield');
	this.displayfield = WDG_getAttributeNS(this.input, 'displayfield');
	var group = WDG_getAttributeNS(this.input, 'groupby');
	if (group == "all") {
		group = 1;
	} else {
		group = parseInt(group, 10);
	}

	this.group = isNaN(group)?1:group;

	window[$DCC_GLOBALOBJECT][input] = this;

	this.inspect();
	this.setEnabled(!this.input.disabled);
	try {Kore.addUnloadListener(this.dispose, this);}catch(err){}
}

MXW_CommaCheckboxes_dispose = function() {
	try {Kore.removeUnloadListener(this.dispose, this);} catch(err) {}
	try{delete window[$DCC_GLOBALOBJECT][this.name];}catch(err){}
}
MXW_CommaCheckboxes.prototype.dispose = MXW_CommaCheckboxes_dispose;

function MXW_CommaCheckboxes_setEnabled(state) {
	this.input.disabled = !state;
	for(var i=0; i<this.checkboxes.length; i++) {
		this.checkboxes[i].disabled = !state;
	}
}
MXW_CommaCheckboxes.prototype.setEnabled = MXW_CommaCheckboxes_setEnabled;

function MXW_CommaCheckboxes_paint(forceRepaint) {
	if (typeof forceRepaint=="undefined") {
		forceRepaint = false;
	}
	if(this.painted && !forceRepaint ) {
		return;
	}

	if (forceRepaint && this.container) {
		this.container.innerHTML = "";
		this.container.parentNode.removeChild(this.container);
	}

	this.container = utility.dom.createElement("SPAN", { });
	utility.dom.insertAfter(this.container, this.input);

	
	this.checkboxes = [];
	this.input.style.display = "none";

	var cellCount = 0;

	var theTable = utility.dom.createElement("TABLE", {
		cellPading:0,
		cellSpacing:0,
		border:0
	});
	var curRow = null;
	var cbCounter = 0;
	this.recordset.MoveFirst(0);
	while(this.recordset.MoveNext()) {
		if (curRow == null) {
			curRow = theTable.insertRow( (is.safari ? -1 : theTable.rows.length) );
		}
		var curCell = curRow.insertCell(curRow.cells.length);
		cellCount++;

		lbl = utility.dom.createElement("LABEL", {
			"id"		: this.name +"_label" + cbCounter,
			"htmlFor"		: this.name +"_commacheckbox" + cbCounter
		});
		curCell.appendChild(lbl);

		var cb = utility.dom.createElement("INPUT", {
			"type"	: "checkbox",
			"id"		: this.name +"_commacheckbox" + cbCounter,
			"value": this.recordset.Fields(this.valuefield)
		});
		lbl.appendChild(cb);
		lbl.innerHTML += this.recordset.Fields(this.displayfield);
		//changing innerHTML destroys the cb reference, so we must find cb again
		cb = lbl.firstChild;
		cb.onclick = MXW_CommaCheckboxes_checkbox_click;
		WDG_setAttributeNS(cb, 'cbFor', this.name);
		this.checkboxes[cbCounter] = cb;

		if ( cellCount == this.group ) {
			cellCount = 0;
			curRow = null;
		}
		cbCounter++;
	}
	if (curRow!=null && this.group!=cellCount) {
		curCell = curRow.insertCell(curRow.cells.length);
		curCell.colSpan = this.group-cellCount+1;
		curCell.innerHTML = "&nbsp;";
	}
	this.container.appendChild(theTable);	
	this.painted = true;
}

function MXW_CommaCheckboxes_inspect() {
	this.paint();
	var strValues = this.input.value;
	var arrValues = strValues.split(/,/g);
	for (var i=0; i < arrValues.length; i++) {
		arrValues[i] = String_trim(arrValues[i]);
	}
	for(var i=0; i<this.checkboxes.length; i++) {
		if(Array_indexOf(arrValues, this.checkboxes[i].value) >= 0) {
			window.setTimeout("lateIECBCheck('" + this.checkboxes[i].id + "')", (i+1) * 10);
		} else {
			this.checkboxes[i].checked = false;
		}
	}
}

function lateIECBCheck(cbid) {
	document.getElementById(cbid).checked = true;
	
}
function MXW_CommaCheckboxes_apply() {
	var newValue = "";
	for(var i=0; i<this.checkboxes.length; i++) {
		if(this.checkboxes[i].checked) {
			newValue += (newValue==""?"":",") + this.checkboxes[i].value;
		}
	}
	this.input.value = newValue;

	try{
		if (this.input.fireEvent) {
			this.input.fireEvent("onchange");
		} else if(document.createEvent){
			var me = document.createEvent("Events");
			me.initEvent('change', 0, 0);
			this.input.dispatchEvent(me);
		}
	}catch(err) { }
}
function MXW_CommaCheckboxes_checkbox_click() {
	window[$DCC_GLOBALOBJECT][WDG_getAttributeNS(this, 'cbFor')].apply();
}
MXW_CommaCheckboxes.prototype.paint = MXW_CommaCheckboxes_paint;
MXW_CommaCheckboxes.prototype.apply = MXW_CommaCheckboxes_apply;
MXW_CommaCheckboxes.prototype.inspect = MXW_CommaCheckboxes_inspect;

