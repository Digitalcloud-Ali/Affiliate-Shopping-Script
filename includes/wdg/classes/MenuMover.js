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

$MMO_MAIN_CLASSNAME = "MenuMover";
$MMO_GLOBALOBJECT = "MenuMovers";
$MMO_SELECT_SIZE_OPTION_NAME = "size";

if (typeof window[$MMO_GLOBALOBJECT] == 'undefined') {
	window[$MMO_GLOBALOBJECT] = {};
}


function MXW_MenuMover(input) {
	if (is.safari && is.version < 1.4) {
		return;
	}
	var originalElement = document.getElementById(input);
	if (typeof(originalElement.kt_uni_attached) == 'undefined') {
		originalElement.kt_uni_attached = true;
	} else {
		return;
	}

	this.input = originalElement;
	this.name = input;
	this.input.widget_id = this.name;
	this.input.widget_type = $MMO_GLOBALOBJECT;

	this.recordset = new JSRecordset(WDG_getAttributeNS(this.input, 'recordset'));

	this.valuefield = WDG_getAttributeNS(this.input, 'valuefield');
	this.displayfield = WDG_getAttributeNS(this.input, 'displayfield');

	var ss = parseInt(this.input.size);
	this.select_size = isNaN(ss)?8:ss;

	this.initialize();
	this.render();

	window[$MMO_GLOBALOBJECT][input] = this;
	this.setEnabled(!this.input.disabled);
	try {Kore.addUnloadListener(this.dispose, this);}catch(err){}
}

MXW_MenuMover_dispose = function() {
	try {Kore.removeUnloadListener(this.dispose, this);} catch(err) {}
	try{delete window[$MMO_GLOBALOBJECT][this.name];}catch(err){}
}
MXW_MenuMover.prototype.dispose = MXW_MenuMover_dispose;

function MXW_MenuMover_setEnabled(state) {
	this.input.disabled = !state;
	this.selectFrom.disabled = !state;
	this.selectTo.disabled = !state;

	this.rightbutton.disabled = !state;
	this.leftbutton.disabled = !state;
	this.leftallbutton.disabled = !state;
	this.rightallbutton.disabled = !state;
	if (this.baselistsorter) {
		this.baselistsorter.setEnabled(state);
	}
}
MXW_MenuMover.prototype.setEnabled = MXW_MenuMover_setEnabled;

MXW_MenuMover.prototype.initialize = function() {
}

MXW_MenuMover.prototype.render = function() {
	this.input.style.display = "none";

	this.selectFrom = utility.dom.createElement("SELECT", {
		multiple		: true,
		className:$MMO_MAIN_CLASSNAME + "_select",
		size		: this.select_size
	});

	this.selectTo = utility.dom.createElement("select", {
		multiple		: true,
		className:$MMO_MAIN_CLASSNAME + "_select",
		size		: this.select_size,
		id	:this.name + "_selecTo"
	});

	// create the buttons
	this.rightbutton = utility.dom.createElement('input', {
		type:'button',
		className:$MMO_MAIN_CLASSNAME + "_button",
		value:' > '
	});

	this.leftbutton = utility.dom.createElement('input', {
		type:'button',
		className:$MMO_MAIN_CLASSNAME + "_button",
		value: ' < '
	});

	this.leftallbutton = utility.dom.createElement('input', {
		type:'button',
		className:$MMO_MAIN_CLASSNAME + "_button",
		value:'<<'
	});

	this.rightallbutton = utility.dom.createElement('input', {
		type:'button',
		className:$MMO_MAIN_CLASSNAME + "_button",
		value:'>>'
	});

	var theContainer = utility.dom.createElement('span', {className:'MXW_MMO_container widget_container'});
	this.input.parentNode.insertBefore(theContainer, this.input.nextSibling);

	var tmp = document.createElement("DIV");
	tmp.innerHTML = '<table border="0" cellspacing="0" cellpadding="0"><tr><td></td><td><table border="0" cellspacing="2" cellpadding="0"><tr><td></td><td></td></tr><tr><td></td><td></td></tr></table></td><td></td></tr></table>\r\n';
	theContainer.appendChild(tmp.firstChild);
	tmp = null;

	var theTable = theContainer.firstChild;
	var theRow = theTable.rows[0];
	var c1 = theRow.cells[0];
	var c2 = theRow.cells[1];
	var c3 = theRow.cells[2];

	c1.appendChild(this.selectFrom);
	c2.firstChild.rows[0].cells[0].appendChild(this.leftbutton);
	c2.firstChild.rows[0].cells[1].appendChild(this.rightbutton);
	c2.firstChild.rows[1].cells[0].appendChild(this.leftallbutton);
	c2.firstChild.rows[1].cells[1].appendChild(this.rightallbutton);
	c3.appendChild(this.selectTo);


	var obj = this;
	utility.dom.attachEvent(this.selectTo, 'dblclick', function(e){obj.moveLeft();}, 1);
	utility.dom.attachEvent(this.selectFrom, 'dblclick', function(e){obj.moveRight();}, 1);

	utility.dom.attachEvent(this.leftbutton, 'click', function(e){obj.moveLeft();}, 1);
	utility.dom.attachEvent(this.rightbutton, 'click', function(e){obj.moveRight();}, 1);

	utility.dom.attachEvent(this.leftallbutton, 'click', function(e){obj.moveLeft(true);}, 1);
	utility.dom.attachEvent(this.rightallbutton, 'click', function(e){obj.moveRight(true);}, 1);
	this.inspect();
	this.selectFrom.style.width = "150px";

	this.baselistsorter = null;
	if ( (WDG_getAttributeNS(this.input, 'sortselector')+'').toLowerCase() == "yes") {
		this.baselistsorter = new MXW_BaseListSorter(this.name + "_selecTo", simple_move, this.name);
	}
}

MXW_MenuMover.prototype.inspect = function () {
	this.selectFrom.options.length = 0;
	this.selectTo.options.length = 0;
	var selectedValues = this.input.value.split(",");
	Array_each(selectedValues, function(item, i){
		selectedValues[i] = String_trim(selectedValues[i]);
	});
	this.recordset.MoveFirst();
	while (this.recordset.MoveNext()) {
		var oneValue = this.recordset.Fields(this.valuefield);
		var oneText = this.recordset.Fields(this.displayfield);
		var o = new Option(oneText, oneValue);
		if (Array_indexOf(selectedValues, oneValue) == -1) {
			this.selectFrom.options[this.selectFrom.options.length] = o;
		} else {
			this.selectTo.options[this.selectTo.options.length] = o;
		}
	}
	this.selectFrom.selectedIndex = -1;
	this.selectTo.selectedIndex = -1;
}

MXW_MenuMover.prototype.apply = function () {
	var selectedValues = "";
	for (var i=0; i<this.selectTo.options.length; i++ ) {
		selectedValues += (i==0?"":",") + this.selectTo.options[i].value;
	}
	this.input.value = selectedValues;
}

function MXW_MenuMover_moveFromTo (from, to) {
	var indexes = [];
	for (var j = 0; j < from.options.length; j++) {
		if (from.options[j].selected == true) {
			var nv = from.options[j].value;
			var nt = from.options[j].text;
			
			to.options[to.options.length] = new Option(nt, nv);
			Array_push(indexes, j);
		}
	}
	for (var i = indexes.length-1; i >= 0; i--) {
		from.options[indexes[i]].selected = false;
		from.options[indexes[i]] = null;
	}
	from.selectedIndex = -1;
	to.selectedIndex = -1;
}

MXW_MenuMover.prototype.moveLeft = function(moveAll) {
	if (typeof moveAll=="undefined") {
		moveAll = false;
	}
	if (moveAll) {
		for(var i=0; i<this.selectTo.options.length; i++) {
			this.selectTo.options[i].selected = true;
		}
	}
	MXW_MenuMover_moveFromTo(this.selectTo, this.selectFrom);
	this.apply();
}

MXW_MenuMover.prototype.moveRight = function(moveAll) {
	if (typeof moveAll=="undefined") {
		moveAll = false;
	}
	if (moveAll) {
		for(var i=0; i<this.selectFrom.options.length; i++) {
			this.selectFrom.options[i].selected = true;
		}
	}
	MXW_MenuMover_moveFromTo(this.selectFrom, this.selectTo);
	this.apply();
}
