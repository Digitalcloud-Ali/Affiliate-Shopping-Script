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

/*
 *
dependent dropdown
should create two objects : 
	masterselect, only if it doesn't exist
	details select
 */
$DDN_GLOBALOBJECT = 'DependentDropdowns';
if (typeof window[$DDN_GLOBALOBJECT] == 'undefined') {
	window[$DDN_GLOBALOBJECT] = {};
}


function MXW_DependentDropdown(detailSelect) {
	var originalElement = document.getElementById(detailSelect);
	if (typeof(originalElement.kt_uni_attached) == 'undefined') {
		originalElement.kt_uni_attached = true;
	} else {
		return;
	}

	this.detailSelect = originalElement;
	this.masterSelect = document.getElementById(WDG_getAttributeNS(this.detailSelect, 'triggerobject'));

	this.master = new MXW_MasterSelect(this.masterSelect);
	
	this.recordset = new JSRecordset(WDG_getAttributeNS(this.detailSelect, 'recordset'));
	this.fkey = WDG_getAttributeNS(this.detailSelect, 'fkey');
	this.valuefield = WDG_getAttributeNS(this.detailSelect, 'valuefield');
	this.displayfield = WDG_getAttributeNS(this.detailSelect, 'displayfield');
	this.defaultValue = WDG_getAttributeNS(this.detailSelect, 'selected');

	window[$DDN_GLOBALOBJECT][this.detailSelect.id] = this;

	window[$DDR_DEPENDENT_OBJ][this.masterSelect.id + '_' + this.detailSelect.id] = this;
	this.master.connectByName(this, 'updateMe');
	this.initialize();
	try {Kore.addUnloadListener(this.dispose, this);}catch(err){}
}

MXW_DependentDropdown_dispose = function() {
	try {Kore.removeUnloadListener(this.dispose, this);} catch(err) {}
	this.master.disconnectAllByName(this, 'updateMe');
	try{delete window[$DDN_GLOBALOBJECT][this.detailSelect.id];}catch(err){}
}
MXW_DependentDropdown.prototype.dispose = MXW_DependentDropdown_dispose;


function MXW_DependentDropdown_initialize() {
	this.defaultOptions = [];
	for (var i=0; i < this.detailSelect.options.length; i++) {
		Array_push(this.defaultOptions, {
			'value': this.detailSelect.options[i].value, 
			'text': this.detailSelect.options[i].text
		});
	}

	if (this.defaultValue) {
		if(this.recordset.find(this.valuefield, "=", this.defaultValue) ) {
			if (typeof window[$DDN_GLOBALOBJECT][this.masterSelect.id] == 'undefined') {
				WDG_setAttributeNS(this.masterSelect, "selected", this.recordset.Fields(this.fkey));
				for (var i=0;i<this.masterSelect.options.length;i++) {
					if (this.masterSelect.options[i].value == this.recordset.Fields(this.fkey)) {
						this.masterSelect.selectedIndex = i;
						this.updateMe();
						break;
					}
				}
			} else {
				var masterdep = window[$DDN_GLOBALOBJECT][this.masterSelect.id];
				masterdep.select(this.recordset.Fields(this.fkey));
			}
		}
	} else {
		this.updateMe();
	}
}

function MXW_DependentDropdown_select(val) {
	this.defaultValue = val;
	WDG_setAttributeNS(this.detailSelect, "selected", val);
	for(var i=0; i<this.detailSelect.options.length; i++) {
		if (this.detailSelect.options[i].value == val) {
			this.updateMe();
			return;
		}
	}
	var par_value = "";

	if (this.recordset.find(this.valuefield, "=", val) ) {
		par_value = this.recordset.Fields(this.fkey);
	} else {
		return;
	}

	if (typeof window[$DDN_GLOBALOBJECT][this.masterSelect.id] != 'undefined') {
		var masterdep = window[$DDN_GLOBALOBJECT][this.masterSelect.id];
		masterdep.select(par_value);
	} else {
		utility.dom.selectOption(this.masterSelect, par_value);
		this.updateMe();
	}
}

function MXW_DependentDropdown_updateMe() {
	var detailSelect = this.detailSelect;
	var masterSelect = this.masterSelect;
	var defaultOptions = this.defaultOptions;
	detailSelect.options.length = 0;

	if (masterSelect.options.length == 0) {
		return;
	}

	// first add defaults
	Array_each(defaultOptions, function(item, i) {
		detailSelect.options[detailSelect.options.length] = 
			new Option(utility.string.getInnerText(item['text']), item['value']);
	});

	// add values
	this.recordset.MoveFirst();
	if (masterSelect.selectedIndex != -1) { 
		var selectedValues = [];
		for(var i=0; i<masterSelect.options.length; i++) {
			if(masterSelect.options[i].selected)  {
				 Array_push(selectedValues, masterSelect.options[i].value);
			}
		}
		var optLength = detailSelect.options.length, selOptIndex = 0;
		while (this.recordset.MoveNext()) {
				if(Array_indexOf(selectedValues, this.recordset.Fields(this.fkey))>=0) {
				var o = new Option(
					utility.string.getInnerText(this.recordset.Fields(this.displayfield)), 
					this.recordset.Fields(this.valuefield)
				);
				optLength++;
				if (this.defaultValue == this.recordset.Fields(this.valuefield)) {
					selOptIndex = optLength - 1;
				}
				detailSelect.options[detailSelect.options.length] = o;
			}
		}
		try { detailSelect.selectedIndex = selOptIndex; this.defaultValue = detailSelect.options[detailSelect.selectedIndex].value;} catch(e) { }
	}
	if (typeof window[$DDR_MASTERSELECT_OBJ][this.detailSelect.id] != 'undefined') {
		window[$DDR_MASTERSELECT_OBJ][this.detailSelect.id].change();
	}
}
MXW_DependentDropdown.prototype.initialize = MXW_DependentDropdown_initialize;
MXW_DependentDropdown.prototype.select = MXW_DependentDropdown_select;
MXW_DependentDropdown.prototype.updateMe = MXW_DependentDropdown_updateMe;
