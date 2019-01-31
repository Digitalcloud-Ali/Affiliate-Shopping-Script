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

if (typeof top.jsWindowRecordsets=="undefined") {
	top.jsWindowRecordsets = [];
}
function JSRecordset(rsName) {
	var rawdata = top['jsRawData_'+rsName];
	var metadata = rawdata[0];
	this.Filter = false;
	this.fields = [];
	this.fieldNameIndex = [];
	for (var i=0; i<metadata.length; i++) {
		//later add more field info; fields and field are objects
		var fieldName = metadata[i];
		Array_push(this.fields, {'name':fieldName});
		//the index of this field value in the values array of one row
		this.fieldNameIndex[fieldName] = i;
	}
	// all but first and last rawdata array elements are the rows
	this.rows = rawdata.slice(1, -1);
	this.origRows = this.rows ;
	//MoveFirst
	this.rowIndex = -1;
	top.jsWindowRecordsets[rsName] = this;
}

/*
* call with index or fieldname
*/
JSRecordset.prototype.Fields = function(fieldIndex) {
	if (typeof fieldIndex == "string") {
		fieldIndex = this.fieldIndexFromName(fieldIndex);
	}
	try {
	return this.rows[this.rowIndex][fieldIndex];
	} catch(e) { }
}

JSRecordset.prototype.Move = function(i) {
	this.rowIndex = i;
}


JSRecordset.prototype.MoveFirst = function() {
	this.rowIndex = -1;
}

JSRecordset.prototype.MoveLast = function() {
	this.rowIndex = this.RecordCount();
}

JSRecordset.prototype.MoveNext = function() {
	this.rowIndex++;
	if (this.EOF()) {
		return false;
	}	else {
		return true;
	}
}

JSRecordset.prototype.MovePrev = function() {
	this.rowIndex--;
	if (this.BOF()) {
		return false;
	}	else {
		return true;
	}
}

JSRecordset.prototype.EOF = function() {
	return this.rowIndex >= this.RecordCount();
}

JSRecordset.prototype.BOF = function() {
	return this.rowIndex < 0;
}

JSRecordset.prototype.RecordCount = function() {
	if (typeof this.intRecordCount=="undefined") {
		this.intRecordCount = this.rows.length;
	}
	return this.intRecordCount;
}

function fsort(a, b) {
	return 1;
}
JSRecordset.prototype.sort = function(sortField, sortHow) {
//	var sarr = this.rows.sort(fsort);
}
JSRecordset.prototype.find = function(searchField, searchCriteria, searchValue) {
	var searchFieldIndex = this.fieldIndexFromName(searchField);
	switch(searchCriteria) {
	case "=":
		for (var i=0; i<this.rows.length;i++) {
			if (this.rows[i][searchFieldIndex] == searchValue) {
				this.rowIndex = i;
				return true;
			}
		}
		break;
	case "begins with":
		//case insensitive search
		searchValue = searchValue.toLowerCase();
		for (var i=0; i<this.rows.length; i++) {
			if (this.rows[i][searchFieldIndex].toLowerCase().indexOf(searchValue) == 0) {
				this.rowIndex = i;
				return true;
			}
		}
		break;
	}
	return false;
}

function JSRecordset_fieldIndexFromName(fieldName) {
	if (typeof(this.fieldNameIndex[fieldName]) != "undefined") {
		fieldIndex = this.fieldNameIndex[fieldName];
	} else {
		fieldIndex = this.fieldNameIndex[fieldName.toLowerCase()];
	}
	return fieldIndex;
}
JSRecordset.prototype.fieldIndexFromName = JSRecordset_fieldIndexFromName;

JSRecordset.prototype.Insert = function(row, index) {
	delete this.intRecordCount;
	var newRow = [];
	for (var i=0; i<this.fields.length; i++) {
		for(var j=0; j<this.fields.length; j++) {
			if (this.fieldIndexFromName(this.fields[j].name) == i) {
				Array_push(newRow, row[this.fields[j].name]);
			}
		}
	}

	Array_push(this.origRows, newRow);
	this.rows = this.origRows;

	this.refreshFilteredData();
}
function JSRecordset_setFilter(fkey, criteria, value) {
	if (typeof fkey == "undefined") {
		this.Filter = null;
	} else {
		this.Filter = {'field':fkey, 'criteria':criteria, 'value':value};
	}
	this.refreshFilteredData();
}

function JSRecordset_refreshFilteredData () {
	delete this.intRecordCount;
	this.MoveFirst();
	var filteredRows = [];
	for(var i=0; i<this.rows.length; i++) {
		this.rowIndex = i;
		if (this.matchFilter()) {
			Array_push(filteredRows, this.rows[i]);
		}
	}
	this.rows = filteredRows;
	this.MoveFirst();
}
JSRecordset.prototype.setFilter = JSRecordset_setFilter;
JSRecordset.prototype.refreshFilteredData = JSRecordset_refreshFilteredData;

function JSRecordset_matchFilter() {
	if (!this.Filter) {
		return true;
	}
	var match = true;
	switch(this.Filter.criteria) {
		case "=":
			match = this.Fields(this.Filter.field) == this.Filter.value;
			break;
		case "begins with":
			match = this.Fields(this.Filter.field).toLowerCase().indexOf(this.Filter.value)==0;
		default:
	}
	return match;
}
JSRecordset.prototype.matchFilter = JSRecordset_matchFilter;
