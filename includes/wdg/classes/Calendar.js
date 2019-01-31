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

$CAL_MAIN_CLASSNAME = 'Calendar';
$CAL_GLOBALOBJECT = "Calendars";
if (typeof window[$CAL_GLOBALOBJECT] == 'undefined') {
	window[$CAL_GLOBALOBJECT] = {};
}

function MXW_Calendar (boundTo) {
	if (is.safari && is.version < 1.4) {
		return;
	}
	var originalElement = document.getElementById(boundTo);
	if (typeof(originalElement.kt_uni_attached) == 'undefined') {
		originalElement.kt_uni_attached = true;
	} else {
		return;
	}

	this.input = originalElement;
	this.input.widget_id = boundTo;
	this.input.widget_type = $CAL_GLOBALOBJECT;

	var oldmask = WDG_getAttributeNS(this.input, 'mask');
	var mask = oldmask.replace(/ t$/, ' tt');
	WDG_setAttributeNS(this.input, 'mask', mask);

	this.readonly = (WDG_getAttributeNS(this.input, 'readonly')+'') == 'true';
	if (this.readonly) {
		this.input.readOnly = true;
	}
	//Calendar does not work on IE 5 on MAc, so we  have only SmartDate, and add spinner
	if (is.ie && is.mac) {
		WDG_setAttributeNS(this.input, 'spinner', 'yes');
		this.sd = new MXW_SmartDate(boundTo, true);
		return this;
	}
	this.sd = new MXW_SmartDate(boundTo, true);

	var paramObj = {};
	paramObj.cache = true;
	paramObj.inputField = boundTo;
	paramObj.button = boundTo + "_btn";
	paramObj.ifFormat = mask2calendar(this.sd.mask);
	paramObj.daFormat = mask2calendar(this.sd.mask);
	paramObj.label = WDG_Messages["calendar_button"];
	paramObj.firstDay = (WDG_getAttributeNS(this.input, 'mondayfirst') == 'true') ? 1 : 0 ;
	paramObj.singleClick = WDG_getAttributeNS(this.input, 'singleclick') == 'true';
	if (/(h|H|i|I|s|t)/.test(mask)) {
		paramObj.showsTime = true;
		paramObj.timeFormat = (/(t)/.test(mask) ? "12" : "24");
	}

	//WDG_setAttributeNS(this.input, 'mask', mask2calendar(paramObj.ifFormat));

	var btnAttributes = {
		"type":"button",
		"name":boundTo+"_btn",
		"id":boundTo+"_btn",
		"value":paramObj.label
	};

	var btnSrcAttributes = WDG_getAttributeNS(this.input, 'suppattrs')+'';
	if (btnSrcAttributes = btnSrcAttributes.match(/[^\s]+='[^']*'/gi)) {
		for (var i=0; i<btnSrcAttributes.length; i++) {
			var oAttr = btnSrcAttributes[i].match(/([^\s]+)='([^']*)'/i);
			if(oAttr) {
				btnAttributes[oAttr[1]] = oAttr[2];
			}
		}
	}
	this.button = utility.dom.createElement("input", btnAttributes);
	utility.dom.insertAfter(this.button, this.input);

	Calendar.setup(paramObj);
	window[$CAL_GLOBALOBJECT][boundTo] = this;

	this.setEnabled(!this.input.disabled);
	try {Kore.addUnloadListener(this.dispose, this);}catch(err){}
}

MXW_Calendar_dispose = function () {
	try {Kore.removeUnloadListener(this.dispose, this);} catch(err) {}
	try {
		if (window.calendar) {
			window.calendar.destroy();
			window.calendar = null;
		}
	} catch(err) {}
	try{delete window[$CAL_GLOBALOBJECT][this.name];}catch(err){}
}

MXW_Calendar.prototype.dispose = MXW_Calendar_dispose;

function MXW_Calendar_setEnabled(state) {
	this.input.disabled = !state;
	this.button.disabled = !state;
}
MXW_Calendar.prototype.setEnabled = MXW_Calendar_setEnabled;

