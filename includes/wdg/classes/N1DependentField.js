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

function MXW_N1DependentField2 (boundId) {
	this.boundEl = document.getElementById(boundId);
	var triggerId = WDG_getAttributeNS(this.boundEl, 'triggerobject');
	registerN1Menu(boundId, triggerId);
}

function MXW_N1DependentField(detailSelect) {
	var originalElement = document.getElementById(detailSelect);
	if (typeof(originalElement.kt_uni_attached) == 'undefined') {
		originalElement.kt_uni_attached = true;
	} else {
		return;
	}

	this.detailSelect = originalElement;
	this.masterSelect = document.getElementById(WDG_getAttributeNS(this.detailSelect, 'triggerobject'));

	if (!this.masterSelect) {
		return;
	}

	this.master = new MXW_MasterSelect(this.masterSelect);
	this.recordset = new JSRecordset(WDG_getAttributeNS(this.detailSelect, 'recordset'));

	this.pkey = WDG_getAttributeNS(this.detailSelect, 'pkey');
	this.valuefield = WDG_getAttributeNS(this.detailSelect, 'valuefield');

	this.defaultValue = WDG_getAttributeNS(this.detailSelect, 'selected');

	window[$DDR_DEPENDENT_OBJ][this.masterSelect.id + '_' + this.detailSelect.id] = this;
	this.master.connectByName(this, 'updateMe');
	this.initialize();
	try {Kore.addUnloadListener(this.dispose, this);}catch(err){}
}

MXW_N1DependentField_dispose = function() {
	try {Kore.removeUnloadListener(this.dispose, this);} catch(err) {}
	this.master.disconnectAllByName(this, 'updateMe');
	try{delete window[$DDR_DEPENDENT_OBJ][this.masterSelect.id + '_' + this.detailSelect.id];}catch(err){}
}

MXW_N1DependentField.prototype.dispose = MXW_N1DependentField_dispose;

/*
* if specified,  the default value of the N1DependentField should select
* the corresponding option in the master select at initialization
*/
MXW_N1DependentField.prototype.initialize = function() {
	if (this.defaultValue && this.recordset.find(this.pkey, "=", this.defaultValue)) {
		for (i=0;i<this.masterSelect.options.length;i++) {
			if (this.masterSelect.options[i].value == this.recordset.Fields(this.pkey)) {
				this.masterSelect.selectedIndex = i;
				break;
			}
		}
	}
	this.updateMe();
}

MXW_N1DependentField.prototype.updateMe = function() {
	if (this.masterSelect.selectedIndex<0) {
		this.detailSelect.value = "";
		return;
	}
	var masterValue = this.masterSelect.options[this.masterSelect.selectedIndex].value;
	var newValue = "";

	if (this.recordset.find(this.pkey, "=", masterValue)) {
		newValue = this.recordset.Fields(this.valuefield);
		if (typeof newValue=="undefined") {
			newValue = "";
		}
	}

	this.detailSelect.value = newValue;
}
