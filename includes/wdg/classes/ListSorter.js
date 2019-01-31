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

//configuration variables

$LIS_VALUES_SEPARATOR = "|";
$LIS_MAIN_CLASSNAME = 'ListSorter';

$LIS_GLOBALOBJECT = "ListSorters";
if (typeof window[$LIS_GLOBALOBJECT] == 'undefined') {
	window[$LIS_GLOBALOBJECT] = {};
}

function MXW_ListSorter(input) {
	var originalElement = document.getElementById(input);
	if (typeof(originalElement.kt_uni_attached) == 'undefined') {
		originalElement.kt_uni_attached = true;
	} else {
		return;
	}

	this.select = originalElement;
	this.name = input;
	this.select.widget_id = this.name;
	this.select.widget_type = $LIS_GLOBALOBJECT;

	this.baseSorter = new MXW_BaseListSorter(input, swap_order_fields);

	this.recordset = new JSRecordset(WDG_getAttributeNS(this.select, 'recordset'));

	this.valuefield = WDG_getAttributeNS(this.select, 'valuefield');
	this.displayfield = WDG_getAttributeNS(this.select, 'displayfield');
	this.orderfield = WDG_getAttributeNS(this.select, 'orderfield');

	this.render();
	this.hidden = this.baseSorter.hidden;
	this.hidden.widget_id = this.name;
	this.hidden.widget_type = $LIS_GLOBALOBJECT;
	this.initialize();
	window[$LIS_GLOBALOBJECT][input] = this;
	this.setEnabled(!this.select.disabled);
}

function MXW_ListSorter_setEnabled(state) {
	this.select.disabled = !state;
	this.baseSorter.setEnabled(state);
}
MXW_ListSorter.prototype.setEnabled = MXW_ListSorter_setEnabled;

MXW_ListSorter.prototype.initialize = function() {
	this.recordset.MoveFirst();
	while(this.recordset.MoveNext()) {
		var id_value = this.recordset.Fields(this.valuefield);
		var display_value = this.recordset.Fields(this.displayfield);
		var order_value = this.recordset.Fields(this.orderfield);
		var o = new Option(display_value, id_value + $LIS_VALUES_SEPARATOR + order_value + $LIS_VALUES_SEPARATOR + order_value);
		this.select.options[this.select.options.length] = o;
	}
}

MXW_ListSorter.prototype.render = function() {
	this.select.options.length = 0;
}

function swap_order_fields (s, dir, indexes, newindexes) {
	for (var i = 0; i < indexes.length; i++) {
		var i1 = indexes[i];
		var i2 = indexes[i]+dir;
		var v1 = s.options[i1].value.split($LIS_VALUES_SEPARATOR);
		var tmp1 = {
			'text':s.options[i1].text, 
			'id':v1[0],
			'original_order':v1[1],
			'order':v1[2]
		};

		var v2 = s.options[i2].value.split($LIS_VALUES_SEPARATOR);
		var tmp2 = {
			'text':s.options[i2].text, 
			'id':v2[0],
			'original_order':v2[1],
			'order':v2[2]
		};

		s.options[i1].text = tmp2.text;
		s.options[i1].value = tmp2.id + $LIS_VALUES_SEPARATOR + tmp2.original_order + $LIS_VALUES_SEPARATOR + tmp1.order;
		s.options[i1].selected = false;

		s.options[i2].text = tmp1.text;
		s.options[i2].value = tmp1.id + $LIS_VALUES_SEPARATOR + tmp1.original_order + $LIS_VALUES_SEPARATOR + tmp2.order;
		s.options[i2].selected = false;
	}

	if (is.opera) {
		var optionHeight = Math.round((s.offsetHeight - (s.style.borderTopWidth + s.style.borderBottomWidth))/ s.size);
	}
	var firstSel = 0;
	for (var i = 0; i < newindexes.length; i++) {
		if(firstSel==0) {
			firstSel = newindexes[i];
			if (is.mozilla) {
				if((firstSel+1) * s.options[0].offsetHeight > s.offsetHeight) {
					s.scrollTop = (firstSel+1) * s.options[0].offsetHeight - s.offsetHeight + 6;
				}
			} else if (is.opera) {
				if (firstSel+1>=s.size) {
					//does't actually work as of 15 March, 2005 (Opera 8.00)
					s.scrollTop = (firstSel+1-s.size) * optionHeight;
				}
			}
		}
		s.options[newindexes[i]].selected = true;
	}
}

