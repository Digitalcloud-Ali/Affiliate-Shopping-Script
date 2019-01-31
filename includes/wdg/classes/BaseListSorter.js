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

$LIS_MAIN_CLASSNAME = 'ListSorter';

$BLIS_GLOBALOBJECT = "BaseListSorters";

//minimum interval between 2 moves, in milliseconds
$DBLCLICK_SPEED = 100;//ms

window[$BLIS_GLOBALOBJECT] = {};

function MXW_BaseListSorter(input, callback, hidden_name) {
	this.select = document.getElementById(input);
	this.name = input;
	this.move_timestamp = new Date();
	if (typeof callback == "undefined") {
		callback = simple_move;
	}
	this.swapOrder = callback;

	if (typeof hidden_name == "undefined") {
		hidden_name = false;
	}
	
	this.hidden_name = hidden_name;

	this.render();
	window[$BLIS_GLOBALOBJECT][input] = this;
	return this;
}

function MXW_BaseListSorter_setEnabled(state) {
	this.upbutton.disabled = !state;
	this.downbutton.disabled = !state;
}
MXW_BaseListSorter.prototype.setEnabled = MXW_BaseListSorter_setEnabled;

MXW_BaseListSorter.prototype.render = function() {
	var input = this.name

	// create the buttons
	this.upbutton = document.createElement('button');
	this.upbutton.innerHTML = '&nbsp;&uarr;&nbsp;';
	this.upbutton.className = $LIS_MAIN_CLASSNAME + "_button";
	
	this.downbutton = document.createElement('button');
	this.downbutton.innerHTML = '&nbsp;&darr;&nbsp;';
	this.downbutton.className = $LIS_MAIN_CLASSNAME + "_button";

	var theContainer = utility.dom.createElement('div', {
		className:"widget_container"
	});

	this.select.parentNode.insertBefore(theContainer, this.select.nextSibling);
	this.select.removeAttribute("name");
	this.select.removeAttribute("NAME");

	var tmp = document.createElement("DIV");
	tmp.innerHTML = '<table cellpadding="0px" cellspacing="0px" border="0px"><tr><td></td><td></td></tr></table>';
	theContainer.appendChild(tmp.firstChild);
	tmp = null;

	var theTable = theContainer.firstChild;
	var theRow = theTable.rows[0];
	var c1 = theRow.cells[0];
	var c2 = theRow.cells[1];

	c1.appendChild(this.select);

	if (this.hidden_name) {
		this.hidden = document.getElementById(this.hidden_name);
	} else {
		this.hidden = utility.dom.createElement("INPUT", {
			"type"	: "hidden",
			"name"		: this.name
		});
		c1.appendChild(this.hidden);
	}
	c2.appendChild(this.upbutton);
	c2.appendChild(utility.dom.createElement("BR", {}));
	c2.appendChild(this.downbutton);
	
	this.select.className = $LIS_MAIN_CLASSNAME;

	utility.dom.attachEvent(this.upbutton, 'click', function(e){utility.dom.stopEvent(e);MXW_BaseListSorter_moveUp(input);return false;}, 1);
	utility.dom.attachEvent(this.downbutton, 'click', function(e){utility.dom.stopEvent(e);MXW_BaseListSorter_moveDown(input);return false;}, 1);
	utility.dom.attachEvent(this.upbutton, 'dblclick', function(e){utility.dom.stopEvent(e);MXW_BaseListSorter_moveUp(input);}, 1);
	utility.dom.attachEvent(this.downbutton, 'dblclick', function(e){utility.dom.stopEvent(e);MXW_BaseListSorter_moveDown(input);}, 1);
}

MXW_BaseListSorter.prototype.updateHidden = function() {
	var hiddenValue = "";
	for (var i=0; i<this.select.options.length; i++) {
		hiddenValue += (i==0?"":",")+this.select.options[i].value;
	}
	this.hidden.value = hiddenValue;
}


function canMove(s, i, dir) {
	var canmove = false;
	if ( dir < 0) {
		for (var j = 0; j < i; j++) {
			if (s.options[j].selected == false) {
				canmove = true;
			}
		}
	} else {
		for (var j = s.options.length-1; j > i; j--) {
			if (s.options[j].selected == false) {
				canmove = true;
			}
		}
	}
	return canmove;
}

function MXW_BaseListSorter_moveDown(input) {
	var jso = window[$BLIS_GLOBALOBJECT][input];
	var now = new Date();
	if (now -  jso.move_timestamp<$DBLCLICK_SPEED) {
		return;
	}
	
	jso.move_timestamp = now;
	var s = jso.select;
	var last_index = s.options.length;
	var indexes = [];
	var newindexes = [];
	for (var i = s.options.length-1; i>= 0 ; i--) {
		if (s.options[i].selected == true) {
			if (canMove(s, i, 1)) {
				Array_push(indexes, i);
				Array_push(newindexes, i+1);
			} else {
				Array_push(newindexes, i);
			}
		}
	}
	jso.swapOrder(s, 1, indexes, newindexes);
	jso.updateHidden();
}

function MXW_BaseListSorter_moveUp(input) {
	var jso = window[$BLIS_GLOBALOBJECT][input];
	var now = new Date();
	if (now -  jso.move_timestamp<$DBLCLICK_SPEED) {
		return;
	}
	
	jso.move_timestamp = now;

	var s = jso.select;
	var last_index = s.options.length;
	var indexes = [];
	var newindexes = [];
	for (var i = 0; i< s.options.length; i++) {
		if (s.options[i].selected == true) {
			if (canMove(s, i, -1)) {
				Array_push(indexes, i);
				Array_push(newindexes, i-1);
			} else {
				Array_push(newindexes, i);
			}
		}
	}
	jso.swapOrder(s, -1, indexes, newindexes);
	jso.updateHidden();
}

function simple_move (s, dir, indexes, newindexes) {
	for (var i = 0; i < indexes.length; i++) {
	
		var i1 = indexes[i];
		var i2 = indexes[i]+dir;
		
		var tmp1 = {
			'text':s.options[i1].text, 
			'id':s.options[i1].value
		};

		var tmp2 = {
			'text':s.options[i2].text, 
			'id':s.options[i2].value
		};

		s.options[i1].text = tmp2.text;
		s.options[i1].value = tmp2.id;
		s.options[i1].selected = false;

		s.options[i2].text = tmp1.text;
		s.options[i2].value = tmp1.id;
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

