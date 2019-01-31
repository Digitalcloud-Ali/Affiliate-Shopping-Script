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

$SLI_TAG_REPLACEMENT = 'input';

$SLI_ATTRNAME_TYPE = 'kt_type';
$SLI_ATTRNAME_SUBTYPE = 'kt_subtype';

$SLI_MAIN_CLASSNAME = 'Slider'
$SLI_CURSOR_CLASSNAME = 'SliderCursor'

$SLI_DEFAULT_WIDTH = 100;
$SLI_DEFAULT_HEIGHT = 10;
$SLI_DEFAULT_LINEHEIGHT = 20;
$SLI_DEFAULT_ORIENTATION = 'horizontal';
$SLI_DEFAULT_MIN = 1;
$SLI_DEFAULT_MAX = 10;
$SLI_DEFAULT_STEP = 1;
$SLI_DEFAULT_SCALETYPE = 'continuous'; // discreet, continuous

$SLI_GLOBALOBJECT = "Sliders";
$IMG_NAME = 'cursor.gif';

// some utility functions

function SLI_univalLoad(e) {
	if (!e) e = window.event;
	if (e.target) targ = e.target;
	else if (e.srcElement) targ = e.srcElement;
	var objs = document.getElementsByTagName($SLI_TAG_REPLACEMENT);
	if (typeof window[$SLI_GLOBALOBJECT] == "undefined") {
		window[$SLI_GLOBALOBJECT] = {
			'objects': [], 
			'active': null
		}
	}
	for ( var i = 0; i < objs.length; i++) {
		var tmp = [];
		if (WDG_getAttributeNS(objs[i], $SLI_ATTRNAME_TYPE) != null && WDG_getAttributeNS(objs[i], $SLI_ATTRNAME_SUBTYPE) != null) {
			if (WDG_getAttributeNS(objs[i], $SLI_ATTRNAME_TYPE).toLowerCase() == 'widget' && WDG_getAttributeNS(objs[i], $SLI_ATTRNAME_SUBTYPE).toLowerCase() == 'slider') {
				tmp[tmp.length] = new Slider(objs[i]);
			}
		}
	}
}

function Slider(el) {
	this.oldinput = el;
	
	var tmpel = el;
	while (tmpel.tagName.toLowerCase() != 'form' && tmpel.tagName.toLowerCase() != 'body')
		tmpel = tmpel.parentNode;
	this.form = tmpel;

	this.oldposition = DOM_getAbsolutePos(el);
	// elements
	this.range = document.createElement('div');
	this.cursor = document.createElement('div');
	
	this.width = parseInt(this.oldinput.style.width) || $SLI_DEFAULT_WIDTH;
	this.height = parseInt(this.oldinput.style.height) || $SLI_DEFAULT_HEIGHT;
	this.lineheight = parseInt(DOM_getStyleProperty(this.oldinput, 'lineHeight')) || $SLI_DEFAULT_LINEHEIGHT;

	this.min = parseInt(WDG_getAttributeNS(this.oldinput, 'min')) || $SLI_DEFAULT_MIN;
	this.max = parseInt(WDG_getAttributeNS(this.oldinput, 'max')) || $SLI_DEFAULT_MAX;
	this.step = parseInt(WDG_getAttributeNS(this.oldinput, 'step')) || $SLI_DEFAULT_STEP;
	this.orientation = WDG_getAttributeNS(this.oldinput, 'orientation') || $SLI_DEFAULT_ORIENTATION;

	var remainder = (this.max - this.min) % this.step
	this.max = this.max - remainder;
	
	this.numvalues = ((this.max - this.min) / this.step) + 1;

	this.cursorsize = {
		'width' : 10, 
		'height': 10 
	};

	//this.precision ( if the scale.width < max - min ( range)

	this.initialize();
	this.render();

	if (typeof window[$SLI_GLOBALOBJECT] == "undefined") {
		window[$SLI_GLOBALOBJECT] = {
			'objects': [], 
			'active': null
		}
	}

	Array_push(window[$SLI_GLOBALOBJECT]['objects'], this);
}

Slider.prototype.initialize = function() {
	//
	this.coords = [];
	
}

Slider.prototype.render = function() {
	this.range.className = $SLI_MAIN_CLASSNAME;
	this.range.style.position = 'absolute';
	this.range.style.overflow = 'hidden';

	this.range.style.width =  this.width+ 'px';
	this.range.style.height =  this.height+ 'px';
	this.range.style.lineHeight =  '40px';
	// remove all children
	while (this.range.firstChild) 
		this.range.removeChild(this.range.lastChild);
	
	this.cursor.className = $SLI_CURSOR_CLASSNAME;
	this.cursor.style.position = 'absolute';
	this.cursor.style.overflow = 'hidden';
	this.cursor.style.width =  this.cursorsize['width'] + 'px';
	this.cursor.style.height =  this.cursorsize['height'] + 'px';
	this.cursor.style.lineHeight =  '40px';
	this.cursor.style.left =  '0px';
	this.cursor.style.top =  '0px';
	
	this.cursorimage = document.createElement('img');
	this.cursorimage.src = $IMG_NAME;
	this.cursorimage.width = '10';
	this.cursorimage.height = '10';
	
	this.cursor.appendChild(this.cursorimage);
	this.range.appendChild(this.cursor);
	
	document.body.appendChild(this.range);
	//this.range.innerHTML = innerhtml;
	
	this.oldinput.style.visibility = 'hidden';
	
	this.range.style.left = this.oldposition.x + 'px';
	this.range.style.top = this.oldposition.y + 'px';

	this.rangepos = {
		'x': parseInt(DOM_getStyleProperty(this.range, 'left')), 
		'y': parseInt(DOM_getStyleProperty(this.range, 'top')), 
		'width': parseInt(DOM_getStyleProperty(this.range, 'width')), 
		'height': parseInt(DOM_getStyleProperty(this.range, 'height')) 
	};

	UNI_attachEvent(this.cursor, 'mousedown', activateDrag, 1);
	UNI_attachEvent(this.cursor, 'mouseup', deactivateDrag, 1);
	UNI_attachEvent(this.cursor, 'mousemove', Drag, 1);

	UNI_attachEvent(this.range, 'mousemove', catchUpDrag, 1);
	UNI_attachEvent(this.range, 'click', sliderJump, 1);
	UNI_attachEvent(document, 'mouseup', deactivateDrag, 1);

}

Slider.prototype.moveCursorTo = function(x, y) { // always pass absolute coords
	var orangepos = {'x': this.rangepos.x, 'y': this.rangepos.y};
	var ocursorpos = {'x': parseInt(DOM_getStyleProperty(this.cursor, 'left')), 'y': parseInt(DOM_getStyleProperty(this.cursor, 'top'))};
	
	var displacement = window[$SLI_GLOBALOBJECT]['elementclicklocation'];

	var newx = x - orangepos.x - displacement.x;
	var newy = y - orangepos.y - displacement.y;

	// restrict
	if (this.orientation == 'horizontal') {
		var minx = -this.cursorsize.width / 2;
		if (newx < minx) {
			newx = minx;
		}
		if (newx >= this.rangepos.width - (this.cursorsize.width / 2)) {
			newx = this.rangepos.width - (this.cursorsize.width / 2);
		}
	} else {
	}
	
	//window.status = sprintf('[x,y]:[%s,%s], orangepos[x,y]:[%s,%s], ocursorpos[x,y]:[%s,%s], newx[x,y]:[%s,%s]', x, y, orangepos.x, orangepos.y, ocursorpos.x, ocursorpos.y, newx, newy);
	if (this.orientation == 'horizontal') {
		this.cursor.style.left = newx + 'px';
	} else {
		this.cursor.style.top = newy + 'px';
	}
	this.updateValue();
}

Slider.prototype.updateValue = function() {
	if (this.numvalues > parseInt(this.range.style.width)) {
		var factor = this.numvalues / parseInt(this.range.style.width);
	} else {
		var factor = parseInt(this.range.style.width) / this.numvalues;
	}
	var idx = parseInt(this.cursor.style.left) + (this.cursorsize.width / 2);
	this.oldinput.value = (factor * idx);
	this.oldinput.onchange();
}

function sliderJump(e) {
	o = DOM_setEventVars(e);
	var jso = find_object_for_range(o.targ);
	if (!jso) {
		jso = find_object_for_cursor(o.targ);
	}

	jso.moveCursorTo(o.posx, o.posy);
}


function activateDrag(e) {
	o = DOM_setEventVars(e);
	var jso = find_object_for_cursor(o.targ);
	var orangepos = {'x': jso.rangepos.x, 'y': jso.rangepos.y};
	var ocursorpos = {'x': parseInt(DOM_getStyleProperty(jso.cursor, 'left')), 'y': parseInt(DOM_getStyleProperty(jso.cursor, 'top'))};

	//alert(sprintf('orangepos[x,y]:[%s,%s], ocursorpos[x,y]:[%s,%s], newx[x,y]:[%s,%s], cursorx[x,y]:[%s, %s]', orangepos.x, orangepos.y, ocursorpos.x, ocursorpos.y, newx, newy, cursorx, cursory));

	var whereinside = {
		'x': o.posx - (ocursorpos.x + orangepos.x), 
		'y': o.posy - (ocursorpos.y + orangepos.y)
	};

	window[$SLI_GLOBALOBJECT]['active'] = o.targ;
	window[$SLI_GLOBALOBJECT]['elementclicklocation'] = whereinside;
}

function deactivateDrag(e) {
	o = DOM_setEventVars(e);
	if (window[$SLI_GLOBALOBJECT]['active']) {
		window[$SLI_GLOBALOBJECT]['active'] = null;
		//window[$SLI_GLOBALOBJECT]['elementclicklocation'] = {'x':0, 'y':0};
	}
}

function Drag(e) {
	if (!window[$SLI_GLOBALOBJECT]['active']) {
		DOM_cancelEvent(e);
		return false;
	}
	var o = DOM_setEventVars(e);	
	var jso = find_object_for_cursor(o.targ);

	try {
		jso.moveCursorTo(o.posx, o.posy);
	} catch(e) {
	}

	return false;
}

function catchUpDrag(e) {
	if (!window[$SLI_GLOBALOBJECT]['active']) {
		DOM_cancelEvent(e);
		return false;
	}
	var o = DOM_setEventVars(e);	
	try {
	var jso = find_object_for_range(o.targ);
	var cursorpos = DOM_getAbsolutePos(jso.cursor);
		var cursor_between = (o.posx >= cursorpos.x) && (o.posx <= cursorpos.x + jso.cursorsize.width);

		if (!cursor_between) {
			sliderJump(e);
			DOM_cancelEvent(e);
			return false;
		}
	} catch(e) {
	}
}

function find_object_for_range(el) { //ALWAYS PASS THE RANGE!!!!!!!!!!!!!
	var toret = null;
	for ( var i = 0; i < window[$SLI_GLOBALOBJECT]['objects'].length; i++) {
		if (window[$SLI_GLOBALOBJECT]['objects'][i].range == el || window[$SLI_GLOBALOBJECT]['objects'][i].cursorimage == el) {
			toret = window[$SLI_GLOBALOBJECT]['objects'][i];
			break;
		}
	}
	return toret;
}
function find_object_for_cursor(el) { //ALWAYS PASS THE RANGE!!!!!!!!!!!!!
	var toret = null;
	for ( var i = 0; i < window[$SLI_GLOBALOBJECT]['objects'].length; i++) {
		if (window[$SLI_GLOBALOBJECT]['objects'][i].cursor == el || window[$SLI_GLOBALOBJECT]['objects'][i].cursorimage == el) {
			toret = window[$SLI_GLOBALOBJECT]['objects'][i];
			break;
		}
	}
	return toret;
}
