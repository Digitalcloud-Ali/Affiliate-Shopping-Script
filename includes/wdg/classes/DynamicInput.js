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

$DYS_MAIN_CLASSNAME = 'DynamicObject';
$DYS_GLOBALOBJECT = 'DynamicObject';

if (typeof window[$DYS_GLOBALOBJECT] == 'undefined') {
	window[$DYS_GLOBALOBJECT] = {};
}

function MXW_DynamicSearch(input) {
	if (! ( 
			(is.windows && is.ie) 
			|| (is.mozilla)
			|| is.opera
			|| is.safari
	)) {
		return;
	}
	var tmp = new MXW_DynamicObject(input, 'S', true);
	return tmp;
}

function MXW_DynamicInput(input) {
	if (! ( 
			(is.windows && is.ie) 
			|| (is.mozilla)
			|| is.opera
			|| is.safari
	)) {
		return;
	}
	var tmp = new MXW_DynamicObject(input, 'E', (WDG_getAttributeNS(document.getElementById(input), 'restrict')+'').toLowerCase()=="yes");
	return tmp;
}

var editabledropdowncount = 0;
function MXW_DynamicObject(input, edittype, restrict) {
	this.name = input;
	var originalElement = document.getElementById(input);

	if (typeof(originalElement.kt_uni_attached) == 'undefined') {
		originalElement.kt_uni_attached = true;
	} else {
		return window[$DYS_GLOBALOBJECT][input];
	}

	this.oldinput = originalElement;
	this.edittype = edittype;
	this.tab_pressed = false;
	var toshow = "";
	this.doctypeswitchfix = 0;
	WDG_Messages["dyn_default_option_text"] = utility.string.getInnerText(WDG_Messages["dyn_default_option_text"]);
	this.defaultOptionText = (WDG_getAttributeNS(this.oldinput,'defaultoptiontext')+'').toLowerCase() == "yes"?
															WDG_Messages["dyn_default_option_text"]
															:
															"";
	//use the first option from the original select as the intial text only if nothing is selected
	var old_value = WDG_getAttributeNS(this.oldinput, 'selected');
	if (this.edittype == 'E') {
		if (old_value != null && old_value != '') {
			this.oldvalue = old_value;
		} else if (this.oldinput.selectedIndex>=0) {
			this.oldvalue = this.oldinput.options[this.oldinput.selectedIndex].value;
			toshow = this.oldinput.options[this.oldinput.selectedIndex].text;
		} else {
			this.oldvalue = '';
			toshow = this.defaultOptionText;
		}
	} else {
		if (old_value != null	&& old_value != '') {
			toshow = old_value;
		} else {
			toshow = this.defaultOptionText?this.defaultOptionText:this.oldinput.value;
		}
		if (this.defaultOptionText == toshow) {
			this.oldvalue = '';
		} else {
			this.oldvalue = toshow;
		}
	}

	window['dynImp_submittext'] = utility.string.getInnerText(WDG_Messages["dyn_submit_text"]);
	if (this.edittype == 'E') {
		window['dynImp_ext'] = WDG_getAttributeNS(this.oldinput, 'ext');
	}


	this.recordset = WDG_getAttributeNS(this.oldinput, 'recordset');
	this.recordset = new JSRecordset(this.recordset);
	this.displayfield = WDG_getAttributeNS(this.oldinput, 'displayfield');
	if (this.edittype == 'S') {
		this.valuefield = this.displayfield;
	} else {
		this.valuefield = WDG_getAttributeNS(this.oldinput, 'valuefield');
	}

	if (this.edittype == 'E') {
		var rowCount = this.recordset.RecordCount();
		this.recordset.Move(0);
		var j = 0;
		this.oldinput.options.length = 0;
		if ((is.safari || is.opera) && this.defaultOptionText) {
			this.oldinput.options[this.oldinput.options.length] = new Option(this.defaultOptionText, '');
		}
		for(var i = 0;i < rowCount; i++) {
			this.oldinput.options[this.oldinput.options.length] = new Option(this.recordset.Fields(this.displayfield), this.recordset.Fields(this.valuefield));
			if ( this.oldvalue == this.recordset.Fields(this.valuefield)) {
				this.oldinput.options[this.oldinput.options.length-1].selected = true;
				toshow = this.recordset.Fields(this.displayfield);
			}
			this.recordset.MoveNext();
		}
	}

	if (is.safari || is.opera) {
		return;
	}
	//
	this.oldinput.widget_id = this.name;
	this.oldinput.widget_type = $DYS_GLOBALOBJECT;

	this.restrict = restrict;
	this.edittype = edittype;

	var nr = parseInt(WDG_getAttributeNS(this.oldinput, 'norec'));
	this.norec = isNaN(nr) ? 50 : nr;
	this.altstyle = typeof(this.oldinput.style.cssText)!='undefined'?this.oldinput.style.cssText:'';
	this.addLabel = WDG_Messages["dyn_add_label_text"];

	this.singleclickselect = (WDG_getAttributeNS(this.oldinput, 'singleclickselect')+'').toLowerCase()!="no";

	//container
	this.container = utility.dom.createElement('span', {
		className:"widget_container"
	});
	this.container = utility.dom.insertAfter(this.container, this.oldinput);
	this.container.innerHTML = '<table class="MXW_Spin_table" border="0" cellpadding="0" cellspacing="0"><tr><td class="MXW_Spin_table_td"></td><td class="MXW_Spin_table_td"></td><td class="MXW_Spin_table_td"></td></tr></table>';

	// the editable input
	this.edit = utility.dom.createElement("INPUT", {
		"type": "text",
		"id": input + "_edit",
		"autocomplete": "off",
		"disableAutocomplete": true,
		"className"	: this.oldinput.className,
		"style"	: this.altstyle,
		"value": toshow,
		"recordset": this.recordset,
		"field": this.displayfield,
		"idfield": this.valuefield,
		"restrict": this.restrict,
		"norec": this.norec,
		"edittype": this.edittype
	});

	this.edit = this.container.firstChild.rows[0].firstChild.appendChild(this.edit);
	//this.edit = utility.dom.insertAfter(this.edit, this.oldinput);
	this.edit.disableAutocomplete = true;
	var obj = this;
	utility.dom.attachEvent(obj.edit, "blur", function (e){
		MXW_DynamicObject_edit_blur(input, e);
	}, 1);
	utility.dom.attachEvent(obj.edit, "focus", function (e){
		MXW_DynamicObject_edit_focus(input, e);
	}, 1);
	utility.dom.attachEvent(obj.edit, is.mozilla?"keypress":"keydown", function (e){
		return MXW_DynamicObject_edit_keydown(input, e);
	}, 1);
	utility.dom.attachEvent(obj.edit, "keyup", function (e){
		MXW_DynamicObject_edit_keyup(input, e);
	}, 1);
	utility.dom.attachEvent(obj.edit, "click", function (e){
		utility.dom.stopEvent(e);
		return false;
	}, 1);
	utility.dom.attachEvent(obj.edit, "mouseup", function (e){
		if (obj.edit.value == obj.defaultOptionText) {
			MXW_setSelectionRange(obj.edit, 0, obj.edit.value.length);
			utility.dom.stopEvent(e);
			return false;
		}
	}, 1);
	// the v button
	if (is.ie) {
		var bw = obj.edit.borderRightWidth?obj.edit.borderRightWidth:obj.edit.borderWidth?obj.edit.borderWidth:2;
		var oh = obj.edit.offsetHeight;
		var ow = oh-2;
		var style = 'height: ' + (oh-1)+ 'px; width: '+(ow)+'px';
		var value = ' ';
		obj.edit.style.width = (obj.edit.offsetWidth - ow) + 'px'
		obj.edit.style.borderRightWidth = '0px';
	} else {
		var bw = obj.edit.borderRightWidth?obj.edit.borderRightWidth:obj.edit.borderWidth?obj.edit.borderWidth:2;
		var oh = obj.edit.offsetHeight;
		var ow = oh-2;
		var style = 'height: ' + (oh-1)+ 'px; width: '+ow+'px';
		var value = ' ';
		obj.edit.style.width = (obj.edit.offsetWidth - ow) + 'px'
		obj.edit.style.borderRightWidth = '0px';
	}
	this.button = utility.dom.createElement("INPUT", {
		"type":"button",
		"id"		: input +"_v",
		"class":"mxw_v",
		"tabindex"	:"-1",
		"ztabIndex"	:-1,
		"value"	:value,
		"style"	:style
	});
	this.button = this.container.firstChild.rows[0].firstChild.nextSibling.appendChild(this.button);
	//this.button = utility.dom.insertAfter(this.button, this.edit);
	var obj2 = this.button;
	utility.dom.attachEvent(obj2, "focus", function (e){
		MXW_DynamicObject_button_focus(input, e);
	}, 1, false, false);
	this.button.tabIndex = "z";
	utility.dom.attachEvent(obj2, "mousedown", function (e){
		utility.dom.stopEvent(e);
		MXW_DynamicObject_button_mousedown(input, e);
		return false;
	}, 1, false, false);

	utility.dom.attachEvent(obj2, "mouseup", function (e){
		obj.edit.focus();
		if (is.ie && is.windows) {
			var rng = obj.edit.document.selection.createRange();
			rng.collapse(false);
			rng.moveStart("character", obj.edit.value.length);
			rng.moveEnd("character", obj.edit.value.length);
			rng.select();
		}
	}, 1, false, false);

	// the add button
	this.addButton = utility.dom.createElement("INPUT", {
		"type"	:"button",
		"id"		: input +"_add",
		"class"	:"mxw_add",
		"style"	: 'height: ' + (oh-1)+ 'px',
		"disabled"	:true,
		"value"	:this.addLabel
	});
	this.addButton = this.container.firstChild.rows[0].lastChild.appendChild(this.addButton);
	this.addButton.style.display = this.restrict?"none":""
	
	var obj3 = this.addButton;
	if (!this.restrict) {
		this.editabledropdown_id = editabledropdowncount++;
		utility.dom.attachEvent(obj3, "click", function (e){MXW_DynamicObject_addButton_click(input, utility.string.getInnerText(WDG_Messages["dyn_are_you_sure_text"]));}, 1, false, false);
	}

	originalElement.style.display = "none";

	/*
	this.dbg = utility.dom.createElement("INPUT", {
		"type"	: "hidden",
		"name"		: input + "_hidden"
	});
	document.body.appendChild(this.dbg);
	*/

	window[$DYS_GLOBALOBJECT][input] = this;

	this.initialize();
	if (!this.restrict) {
		MXW_DynamicObject_updateForm(input);
	}
	this.attachTriggerObject()
	//this.updateFirst();
	this.currentAddButtonDisabled = true;
	this.setEnabled(!this.oldinput.disabled);
	try {Kore.addUnloadListener(this.dispose, this);}catch(err){}
}

function MXW_DynamicObject_setEnabled(state) {
	this.oldinput.disabled = !state;
	this.edit.disabled = !state;
	this.button.disabled = !state;
	if (!state) { 
		utility.dom.classNameAdd(this.button, 'MXW_disabled')
	} else {
		utility.dom.classNameRemove(this.button, 'MXW_disabled')
	}
	if (!this.restrict) {
		if (!state) {
			//save current addButton disabled state
			this.currentAddButtonDisabled = this.addButton.disabled;
			this.addButton.disabled = !state;
		} else {
			this.addButton.disabled = this.currentAddButtonDisabled;
		}
		if (!state) { 
			utility.dom.classNameAdd(this.addButton, 'MXW_disabled')
		} else {
			utility.dom.classNameRemove(this.addButton, 'MXW_disabled')
		}
	}
}
MXW_DynamicObject.prototype.setEnabled = MXW_DynamicObject_setEnabled;

function MXW_DynamicObject_dispose() {
	try {Kore.removeUnloadListener(this.dispose, this);} catch(err) {}
	try{this.oldinput.parentNode.removeChild(this.oldinput);}catch(err){}
	try{this.container.parentNode.removeChild(this.container);}catch(err){}
	try{this.edit.parentNode.removeChild(this.edit);}catch(err){}
	try{this.button.parentNode.removeChild(this.button);}catch(err){}
	try{this.addButton.parentNode.removeChild(this.addButton);}catch(err){}
	try{this.selcont.parentNode.removeChild(this.selcont);}catch(err){}
	try{this.sel.parentNode.removeChild(this.sel);}catch(err){}
	try{this.iframe.parentNode.removeChild(this.iframe);}catch(err){}
	try{delete window[$DYS_GLOBALOBJECT][this.name];}catch(err){}
}

MXW_DynamicObject.prototype.dispose = MXW_DynamicObject_dispose;

function MXW_DynamicObject_attachTriggerObject() {
	var triggerObjectName = WDG_getAttributeNS(this.oldinput, 'triggerobject')
	if (triggerObjectName) {
		//must have the fkey attribute when having a triggerobject for dependent updates
		this.fkey = WDG_getAttributeNS(this.oldinput, 'fkey');
		if (!this.fkey) {
			return;
		}
		var triggerObject = document.getElementById(triggerObjectName);
		if (!triggerObject) {
			return;
		}
		this.triggerObject = triggerObject;
		if (typeof window[$DDR_MASTERSELECT_OBJ][triggerObject.id] == 'undefined') {
			var tmp = new MXW_MasterSelect(triggerObject);
		} else {
			var tmp = window[$DDR_MASTERSELECT_OBJ][triggerObject.id];
		}
		__sig__.connectByName(tmp, 'change', this, 'updateMe');
	}
}

MXW_DynamicObject.prototype.attachTriggerObject = MXW_DynamicObject_attachTriggerObject;

function MXW_DynamicObject_initialize() {
	this.lastMatch = -1;
	var input = this.name;

	var obj = this;

	var selcont = utility.dom.createElement("div", {
		"id"		: input +"_selcont",
		"style"	: "position:absolute;top:-2000px; visibility:hidden; border:1px solid black; z-index:999; margin:0px; padding:0px;"
	});

	this.selcont = document.body.appendChild(selcont);

	this.fix1 = is.ie?3:2;
	this.fix2 = is.ie?4:4;

	var text = utility.dom.createElement("select", {
		"tabIndex"	: "-1",
		"id"		: input +"_sel",
		"size":"5",
		"style"	: this.altstyle + "; border-width:0px; margin;0px; z-index:999;"
	});

	text.className = this.oldinput.className + " seldrop";
	if (is.mozilla && is.mac) {
		//text.style.display = "none";
	}

	if (is.opera) {
		//it seems that the previous has no efect on opera, so we set style here;
		text.style.visibility = "hidden";
		text.style.position = "absolute";
		text.style.zIndex = 999;
		text.tabIndex = -1;
	}
	text.onmousedown = function(e){
		if(typeof(e)=="undefined") {
			e = event;
		}
		utility.dom.stopEvent(e);

		if(is.mozilla && e.target && e.target.tagName=="scrollbar") {
		} else {
			if(typeof(e)=="undefined") {
				e = event;
			}
			return MXW_DynamicObject_cancelOpenSelect(e);
		}
	};

	var zEdit = this.edit;
	text[this.singleclickselect?'onclick':'ondblclick'] = function (e){
		MXW_DynamicObject_listClicked(input);
		MXW_DynamicObject_listDblClicked(input, e);
		zEdit.focus();
	};

	this.sel = this.selcont.appendChild(text);
	this.virtualStart = 0;

	if (this.edittype == 'E' && !this.restrict) {
		var ifr = utility.dom.createElement("iframe", {
			'id': input + '_iframe', 
			'style': "display:none; width:300px; height:300px;", 
			'src': "includes/wdg/WDG." + dynImp_ext
		});
		this.iframe = document.body.appendChild(ifr);
		if (is.opera) {
			this.iframe.style.display = "none";
		}
	}
	if (is.ie) {
		var tstEl = null;
		if (!(tstEl=document.getElementById("doctypeswitchtest"))) {
			tstEl = utility.dom.createElement("div", {
				"id":"doctypeswitchtest",
				"style":'position:absolute !important; top:20px !important; left:20px !important; \
				margin:1px !important; \
				padding:2px !important; \
				border:4px solid black;\
				width:100px !important; \
				border-right-width:0px !important;\
				padding-right:0px !important;\
				margin-right:0px !important;\
				height:20px !important; \
				visibility:hidden;'
			});
			tstEl = document.body.appendChild(tstEl);
		}
		var tst = tstEl.offsetWidth - 100;
		if (tst === 0) {
			this.doctypeswitchfix = 2;
		}
	}
}
MXW_DynamicObject.prototype.initialize = MXW_DynamicObject_initialize;

window.to = new Array();
function moveXbySlicePos(x, img) {
	if (!document.layers) {
		var macIE45 = is.mac && is.v >= 4.5 && is.v<5.2;
		var par = img;
		var lastOffset = 0;
		while (par) {
			if( par.leftMargin && ! is.windows ) x += parseInt(par.leftMargin);
			if( (par.offsetLeft != lastOffset) && par.offsetLeft ) x += parseInt(par.offsetLeft);
			if( par.offsetLeft != 0 ) lastOffset = par.offsetLeft;
			par = macIE45 ? par.parentElement : par.offsetParent;
		}
	} else if (img.x) x += img.x;
	return x;
}

function moveYbySlicePos(y, img) {
	if (!document.layers) {
		var macIE45 = is.mac && is.v >= 4.5 && is.v<5.2;
		var par = img;
		var lastOffset = 0;
		while (par) {
			if( par.topMargin && !is.windows ) y += parseInt(par.topMargin);
			if( (par.offsetTop != lastOffset) && par.offsetTop ) y += parseInt(par.offsetTop);
			if( par.offsetTop != 0 ) lastOffset = par.offsetTop;
			par = macIE45 ? par.offsetParent : par.offsetParent;
		}
	} else if (img.y >= 0) y += img.y;
	return y;
}

function MXW_DynamicObject_drawRange(input, begin, count) {
	var obj = window[$DYS_GLOBALOBJECT][input];
	var rowCount = obj.recordset.RecordCount();
	obj.recordset.Move(begin);

	var j = 0;
	for(var i = begin; j < count && i<rowCount; i++) {
		if (obj.sel.options[j]) {
			if (is.ie)
				obj.sel.options[j].innerText = obj.recordset.Fields(obj.displayfield);
			else
				obj.sel.options[j].text = obj.recordset.Fields(obj.displayfield);
			obj.sel.options[j].value = obj.recordset.Fields(obj.valuefield);
		} else {
			obj.sel.options[obj.sel.options.length] = new Option(obj.recordset.Fields(obj.displayfield), obj.recordset.Fields(obj.valuefield));
		}

		obj.recordset.MoveNext();
		j++;
	}

}
function MXW_DynamicObject_cancelOpenSelect(e) {
	var open_input = window[$DYS_GLOBALOBJECT]['openelement'];
	if (open_input+""=="undefined") {
		return true;
	}
	if (typeof(e)!="undefined") {

	} else if(typeof event!="undefined"){
		e = event;
	}
	if (typeof(e)!="undefined") {
		var o = utility.dom.setEventVars(e);
		var t = o.targ;
		if (t.tagName.toLowerCase() == "option" || t.tagName.toLowerCase()=="scrollbar") {
			while(t && t.tagName.toLowerCase() !="select") {
				t = t.parentNode;
			}
		}
		if (t) {
			if (t.id 
				&& (t.id == open_input + "_edit"
						|| t.id == open_input + "_v"
						|| t.id == open_input + "_sel"
					)
				) {
				utility.dom.stopEvent(o.e);
				return false;
			}
		}
	}
	//_t("Close :" + open_input);

	window[$DYS_GLOBALOBJECT]['openelement'] = "undefined";
	MXW_DynamicObject_closeSelect(open_input);
	return true;
}

function MXW_DynamicObject_simpleClose() {
	var open_input = window[$DYS_GLOBALOBJECT]['openelement'];
	if (open_input+""=="undefined") {
		return true;
	}

	window[$DYS_GLOBALOBJECT]['openelement'] = "undefined";
	MXW_DynamicObject_closeSelect(open_input);
	return true;

}
function MXW_DynamicObject_openSelect(input) {
	MXW_DynamicObject_cancelOpenSelect();
	window[$DYS_GLOBALOBJECT]['openelement'] = input;
	var obj = window[$DYS_GLOBALOBJECT][input];
	// do not let user to add in DB. only after closing
	obj.addButton.disabled = true;

	obj.virtualStart = 0;

	// add the options
	MXW_DynamicObject_drawRange(input, obj.virtualStart, obj.norec);
	// select the first matching option
	MXW_DynamicObject_syncSelection(input, true);

	//This is a dirty HACK put here to correct a IE6 repaint BUG
	var pos = new Object();
	pos.x = moveXbySlicePos(0, obj.edit);
	pos.y = moveYbySlicePos(obj.edit.offsetHeight, obj.edit);

	if(obj.sel.style.position !="absolute") {
		if (is.mozilla && is.mac) {
			obj.selcont.style.display = 'block';
		}
		obj.selcont.style.visibility = 'visible';
		var nw = obj.edit.offsetWidth + obj.button.offsetWidth - 2;
		var nh = obj.sel.offsetHeight;
		obj.sel.style.position = "absolute";
		obj.sel.style.width = (obj.fix1 + nw + obj.fix1) + 'px';
		obj.sel.style.clip='rect(' + obj.fix1 + 'px ' + (obj.sel.offsetWidth - obj.fix1) + 'px ' + (obj.sel.offsetHeight-obj.fix1) + 'px ' + obj.fix1 + 'px)';
	
		obj.sel.style.left = (-obj.fix1) + "px";
		obj.sel.style.top = (-obj.fix1) + "px";
	
		obj.selcont.style.width = (obj.sel.offsetWidth - 2 * obj.fix1 + obj.doctypeswitchfix) + "px";
		obj.selcont.style.height = (obj.sel.offsetHeight - 2 * obj.fix1 + obj.doctypeswitchfix) + "px";
	}
	obj.selcont.style.position = "absolute";
	obj.selcont.style.left = (pos.x + obj.doctypeswitchfix) + "px";
	obj.selcont.style.top = pos.y + "px";

	if (is.mozilla && is.mac) {
		obj.selcont.style.display = 'block';
	}
	obj.selcont.style.visibility = 'visible';
}

function MXW_DynamicObject_closeSelect(input) {
	var obj = window[$DYS_GLOBALOBJECT][input];
	if (obj.selcont) {
		obj.selcont.style.visibility = 'hidden';
		if (is.mozilla && is.mac) {
			obj.selcont.style.display = 'none';
		}
		obj.className = 'seldrop';
		if (obj.sel.selectedIndex>=0) {
			obj.newvalue = obj.sel.value;
		}
	}
 window[$DYS_GLOBALOBJECT]['openelement'] = "undefined";
}
function MXW_DynamicObject_dirtyForm(input) {
	var obj = window[$DYS_GLOBALOBJECT][input];
	try {
		obj.oldinput.form.setAttribute('haschanged', '1');
	} catch(e) { }
}
/**
* synchronize text with selection
*
* @param 
*	el - editable element
*/
function MXW_DynamicObject_syncWithSelection(input) {
	var obj = window[$DYS_GLOBALOBJECT][input];
	
	if (obj.sel.selectedIndex == -1) {
		return;
	}
	MXW_DynamicObject_setAddDisabled(input, true);
	obj.newvalue = obj.sel.options[obj.sel.selectedIndex].value;
	obj.edit.value = obj.sel.options[obj.sel.selectedIndex].text;
	obj.oldinput.value = obj.sel.options[obj.sel.selectedIndex].value;
	if (!obj.firstFocus) {
		MXW_DynamicObject_dirtyForm(input);
	}
	var offset = obj.edit.value.length;
	if (obj.edit.setSelectionRange) {
		// mozilla
		obj.edit.setSelectionRange(offset, offset); 
	} else if (obj.edit.createTextRange) {
		// IE
		var range = obj.edit.createTextRange();
		range.moveStart('character', offset);
		range.moveEnd('character', offset);
		range.select();
	}
}

function MXW_DynamicObject_setAddDisabled(input, dis) {
	var obj = window[$DYS_GLOBALOBJECT][input];
	if (obj.edittype == 'S' || obj.edittype == 'E' && obj.restrict) {
		return;
	}
	obj.addButton.disabled = dis;
}

/**
* synchronize selection with text 
*
* @param 
*	el - editable element
*	isOpening - true if the selection list is opening
*/
function MXW_DynamicObject_syncSelection(input, isOpening) {
	var obj = window[$DYS_GLOBALOBJECT][input];
	var startTxt = obj.edit.value.toLowerCase();
	if(startTxt.length == 0) {
		obj.sel.selectedIndex = -1;
		MXW_DynamicObject_setAddDisabled(input, true);
		return;
	}

	var rowCount = obj.recordset.RecordCount();
	var firstMatch = -1;

	if (obj.recordset.find(obj.displayfield, "begins with", startTxt)) {
		firstMatch = obj.recordset.rowIndex;
	}
	//if exact match disable add button 
	if(firstMatch>=0 && obj.recordset.Fields(obj.displayfield).length == startTxt.length) {
		MXW_DynamicObject_setAddDisabled(input, true);
	} else {
		if (WDG_Messages["dyn_default_option_text"] != obj.edit.value) {
			MXW_DynamicObject_setAddDisabled(input, false);
		}

		if (obj.edittype == 'E') {
			obj.oldinput.value = '';
		} else {
			if (obj.defaultOptionText == '' || obj.edit.value != obj.defaultOptionText) {
				obj.oldinput.value = obj.edit.value;
			} else {
				obj.oldinput.value = '';
			}
		}

		MXW_DynamicObject_dirtyForm(input);
	}

	if(isOpening && startTxt.length==0) {
		firstMatch = 0;
	}
	if (firstMatch!=-1) {
		obj.lastMatch = firstMatch;
	}
	// see if first match is in list
	if(firstMatch < obj.virtualStart || firstMatch >= obj.virtualStart + obj.norec) {
		// center list on firstMatch
		var tmp = firstMatch - Math.floor(obj.norec/2);
		if (tmp < 1) {
			obj.virtualStart = 0;
		} else if(tmp+obj.norec > rowCount) {
			obj.virtualStart = rowCount-obj.norec;
			firstMatch = obj.norec - (rowCount - firstMatch);
		} else {
			//middle
			obj.virtualStart = tmp;
			firstMatch = Math.floor(obj.norec/2);
		}
		MXW_DynamicObject_drawRange(input, obj.virtualStart, obj.norec);
	} else {
		firstMatch -= obj.virtualStart;
	}

	// select the first matching option
	try {
		obj.sel.selectedIndex = firstMatch;
		if(obj.sel.selectedIndex != firstMatch) {
			MXW_DynamicObject_drawRange(input, obj.virtualStart, obj.norec);
			obj.sel.selectedIndex = firstMatch;
		}
		if (is.mozilla) {
			window.setTimeout('lateFFScrollSetter("' + obj.sel.id + '")', 10);
		}
	} catch (e) { }
}

function lateFFScrollSetter(id) {
	try{
		var sel = document.getElementById(id);
		if (sel.parentNode.style.visibility == "visible") {
			sel.scrollTop = sel.options.selectedIndex * 16 + 1;
		}
	}catch(e){}
}

function MXW_DynamicObject_edit_blur(input, event) {
	var obj = window[$DYS_GLOBALOBJECT][input];
	if (obj.edittype == 'S') {
		if (obj.defaultOptionText == '' || obj.edit.value != obj.defaultOptionText) {
			obj.oldinput.value = obj.edit.value;
		} else {
			obj.oldinput.value = '';
		}
	}
}


function MXW_DynamicObject_edit_focus(input, event) {
	var obj = window[$DYS_GLOBALOBJECT][input];
	if (!obj.firstFocus) {
		MXW_DynamicObject_syncSelection(input);
		utility.dom.stopEvent(event);
		obj.firstFocus = true;
	}
	obj.oldvalue = obj.oldinput.value;
	MXW_setSelectionRange(obj.edit, 0, obj.edit.value.length);
}

//set the hidden value and fire onchange
MXW_DynamicObject.prototype.apply = function() {
	if(this.oldvalue == this.newvalue) {
	//	return;
	}
	this.oldinput.value = this.newvalue;
	MXW_DynamicObject_dirtyForm(this.name);

	if (this.edittype == 'E') {
		var found = false;
		for (var i = 0; i < this.oldinput.options.length; i++) {
			if (this.oldinput.options[i].value == this.newvalue) {
				this.oldinput.selectedIndex = i;
				found = true;
			}
		}
		this.oldinput.value = this.newvalue;
		MXW_DynamicObject_dirtyForm(this.name);
	}
	this.oldvalue = this.newvalue;
	//_t(this.newvalue + ', ' + this.oldinput.value);
	
	if (is.mozilla) {
		window.setTimeout('lateFFScrollSetter("' + this.sel.id + '")', 10);
	}
	if (typeof window[$DDR_MASTERSELECT_OBJ][this.oldinput.id] != 'undefined') {
		window[$DDR_MASTERSELECT_OBJ][this.oldinput.id].change();
	}
}

function MXW_DynamicObject_edit_keydown(input, event) {
	var obj = window[$DYS_GLOBALOBJECT][input];
	var el = obj.edit;
	var ds = obj.name;
	var sel = obj.sel;
	var selcont = obj.selcont;

	obj.tab_pressed = event.keyCode == di_TAB;

	switch (event.keyCode) {
		case di_ESC:
		case di_TAB:
			if (selcont.style.visibility == 'visible') {
				if (sel.selectedIndex != -1) {
					MXW_DynamicObject_syncWithSelection(obj.name);
				}
				MXW_DynamicObject_closeSelect(obj.name);
			} else {
				MXW_setSelectionRange(obj.edit, 0, obj.edit.value.length);
			}
			
			return true;
		case di_ENTER:
			// enter close selection list if is open and submit form if not
			if (sel.selectedIndex != -1) {
				MXW_DynamicObject_syncWithSelection(obj.name);
			}
			if (selcont.style.visibility == 'visible') {
				MXW_DynamicObject_closeSelect(obj.name);
				// stop event propagation (cross browser)
				obj.apply();
				MXW_DynamicObject_edit_blur(input, event);
				utility.dom.stopEvent(event);
				return false;
			} else {
				MXW_DynamicObject_closeSelect(obj.name);
				obj.apply();
				MXW_DynamicObject_edit_blur(input, event);
				if (obj.defaultOptionText == '' || obj.edit.value != obj.defaultOptionText) {
					obj.oldinput.value = obj.edit.value;
				} else {
					obj.oldinput.value = '';
				}
				// submit
				try {
					//obj.oldinput.form.submit();
				} catch(e) { }
				return true;
			}
		case di_DOWN:
			// opens the list or moves selection down 
			if (selcont.style.visibility == 'hidden') {
				MXW_DynamicObject_openSelect(obj.name);
			} else {
				MXW_DynamicObject_listIncrementSel(obj.name);
				MXW_DynamicObject_syncWithSelection(obj.name);
			}
			break;
		case di_UP:
			// moves selection up in list
			if (selcont.style.visibility == 'visible') {
				MXW_DynamicObject_listDecrementSel(obj.name);
				MXW_DynamicObject_syncWithSelection(obj.name);
			}
			break;
		case di_PgUP:
			// moves selection up in list
			if (selcont.style.visibility == 'visible') {
				MXW_DynamicObject_listDecrementSel(obj.name, 5);
				MXW_DynamicObject_syncWithSelection(obj.name);
			}
			break;
		case di_PgDOWN:
			// moves selection down in list
			if (selcont.style.visibility == 'visible') {
				MXW_DynamicObject_listIncrementSel(obj.name, 5);
				MXW_DynamicObject_syncWithSelection(obj.name);
			}
			break;
	}

	return true;
}

function MXW_DynamicObject_edit_keyup(input, evt) {
	var obj = window[$DYS_GLOBALOBJECT][input];

	if(evt.keyCode == di_ENTER) {
		if (obj.edittype != 'E') {
			obj.oldinput.exvalue = obj.edit.value;
		}
		obj.edit.focus();
		obj.edit.select();
		return;
	}

	// if there is no text in the input or the users hit backspace
	if (evt.keyCode == di_BACKSPACE || evt.keyCode == di_DELETE) {
		if (obj.edit.value == '') {
			obj.oldinput.selectedIndex = -1;
		}
		obj.edit.exValue = obj.edit.value;
		obj.newValue = obj.edit.value;
		MXW_DynamicObject_syncSelection(input);
		return;
	}
	//<37,>39,^38,v40
	if (evt.keyCode == di_UP || evt.keyCode == di_RIGHT || evt.keyCode == di_DOWN ||
		evt.keyCode == di_LEFT || evt.keyCode == di_PgUP || evt.keyCode == di_PgDOWN) {
		return;
	}

	// if the exValue is defined and the input value did not change, do nothing (cursor moved, etc)
	if (obj.edit.exValue && obj.edit.exValue == obj.edit.value) {
		return;
	}

	// first sinc components
	MXW_DynamicObject_syncSelection(input);

	// match variable - if there is a match
	// go through the recordset to see if there is a match
	var match = obj.recordset.find(obj.displayfield, "begins with", obj.edit.value.toLowerCase());

	if (match) {
		MXW_DynamicObject_setAddDisabled(input, true);
		// if there is a match
		// Keep the original value of the input in the typedText variable.
		// We'll use only its original length when selecting

		var typedText = obj.edit.value;
		if (evt.keyCode == 16) {
			// ???
			return;
		}
		// set the input value
		obj.edit.value = obj.recordset.Fields(obj.displayfield);
		obj.oldinput.value = obj.recordset.Fields(obj.valuefield);
		MXW_DynamicObject_dirtyForm(input);

		// set the input selection so it looks access-like
		MXW_setSelectionRange(obj.edit, typedText.length, obj.edit.value.length)
		//obj.edit.setSelectionRange(typedText.length, obj.edit.value.length);
	}

	// keep the input value in the exValue variable, so we can catch cursor movement
	obj.edit.exValue = obj.edit.value;
}

function MXW_DynamicObject_button_mousedown(input) {
	var obj = window[$DYS_GLOBALOBJECT][input];
	if (obj.selcont.style.visibility == 'hidden') {
		MXW_DynamicObject_openSelect(input);
	} else {
		MXW_DynamicObject_closeSelect(input);
	}
}

function MXW_DynamicObject_button_focus(input) {
	var obj = window[$DYS_GLOBALOBJECT][input];
	clearTimeout(window.to[input]);
	if (!obj.tab_pressed) {
		window[$DYS_GLOBALOBJECT][input].edit.focus();
	} else {
		obj.tab_pressed = false;
	}
}

function MXW_DynamicObject_addButton_click(input, confirmText) {
	var obj = window[$DYS_GLOBALOBJECT][input];

	var sw = false;
	if (obj.shouldNotAsk || confirmText=='') {
		sw = true;
		obj.shouldNotAsk = false;
	} else {
		confirmText = confirmText.replace("%s", obj.edit.value);
		sw = confirm(confirmText);
	}
	if (sw) {
		var value = obj.edit.value;
		value = escape(value);
		value = value.replace(/\+/gi, '%2B');
		value = value.replace(/\//gi, '%2F');
		obj.iframe.contentWindow.location.replace($MXW_relPath + "includes/wdg/WDG_recordInsert." + dynImp_ext + "?el=" + input + "&id=" + obj.editabledropdown_id + "&text=" + value);
	}
}

/*----------------------------
	Selection List methods
----------------------------*/

function MXW_DynamicObject_listClicked(input) {
	var obj = window[$DYS_GLOBALOBJECT][input];
	if (obj.sel.exScrollTop == null) {
		obj.sel.exScrollTop = 0;
	}
	obj.sel.ex2ScrollTop = obj.sel.exScrollTop;
	obj.sel.exScrollTop = obj.sel.scrollTop;
	MXW_DynamicObject_syncWithSelection(input);
}

function MXW_DynamicObject_listDblClicked(input, e) {
	var obj = window[$DYS_GLOBALOBJECT][input];
	if (obj.sel.ex2ScrollTop != obj.sel.scrollTop) {
		//return;
	}
	obj.apply();
	MXW_DynamicObject_edit_blur(input, e);
	MXW_DynamicObject_closeSelect(input);
}

var mcnt=0;
function MXW_DynamicObject_listIncrementSel(input, amount) {
	var obj = window[$DYS_GLOBALOBJECT][input];
	if(!amount) {
		amount = 1;
	}

	var rowCount = obj.recordset.RecordCount();
	if(obj.sel.selectedIndex < obj.sel.options.length-amount) {
		//selected index inside displayed items
		obj.sel.selectedIndex += amount;
	} else {
		// selection at end shift options up
		if((obj.virtualStart) + obj.norec + amount <= rowCount) {
			// add 'amount' items from virtual
			obj.virtualStart += amount;
			MXW_DynamicObject_drawRange(input, obj.virtualStart, obj.norec);
		} else if((obj.virtualStart) + obj.norec < rowCount) {
			// less then 'amount' items to add from virtual
			obj.virtualStart = rowCount - obj.norec ;
			MXW_DynamicObject_drawRange(input, obj.virtualStart, obj.norec);
			obj.sel.selectedIndex = obj.norec;
			if (is.mozilla) {
				window.setTimeout('lateFFScrollSetter("' + obj.sel.id + '")', 10);
			}
		} else {
			// no more items to add from virtual
		}
	}
}

function MXW_DynamicObject_listDecrementSel(input, amount) {
	var obj = window[$DYS_GLOBALOBJECT][input];
	if(!amount) {
		amount = 1;
	}
	if(obj.sel.selectedIndex >= 0 + amount) {
		obj.sel.selectedIndex -= amount;
	} else {
		// selection at end shift options up
		if(obj.virtualStart > amount) {
			obj.virtualStart-= amount;
			MXW_DynamicObject_drawRange(input, obj.virtualStart, obj.norec);
		} else if (obj.virtualStart > 0) {
			obj.virtualStart = 0;
			MXW_DynamicObject_drawRange(input, obj.virtualStart, obj.norec);
			obj.sel.selectedIndex = 0;
		} else {
			obj.sel.selectedIndex = 0;
		}
	}
	if (is.mozilla) {
		window.setTimeout('lateFFScrollSetter("' + obj.sel.id + '")', 10);
	}
}
if (typeof MXW_DynamicObject_checkSubmit_registered == "undefined") {
	MXW_DynamicObject_checkSubmit_registered = false
}

function MXW_DynamicObject_updateForm(input) {
	var obj = window[$DYS_GLOBALOBJECT][input];
	var bt = obj.addButton;
	if (!bt.form.btns) {
		bt.form.btns = new Array();
	}
	bt.form.btns[bt.form.btns.length] = bt;

	if (!MXW_DynamicObject_checkSubmit_registered) {
		MXW_DynamicObject_checkSubmit_registered = true;
		GLOBAL_registerFormSubmitEventHandler('MXW_DynamicObject_checkSubmit', 5);
	}

//	utility.dom.attachEvent2(bt.form, 'submit', MXW_DynamicObject_checkSubmit);
	/*
	if (!bt.form.onsubmit) {
		bt.form.onsubmit = MXW_DynamicObject_checkSubmit;
	}
	*/
}

function MXW_DynamicObject_checkSubmit(e) {
	var sw = true;
	var button_id = '';
	//var frm = null;
	for (var i in window[$DYS_GLOBALOBJECT]) {
		if (i == 'openelement') {
			continue;
		}
		if (! (is.ie && is.windows)
		&&  ! (is.mozilla)) {
			if (window[$DYS_GLOBALOBJECT][i].oldinput.value == '') {
				continue;
			}
		}
		//if (frm == null) {
			frm = window[$DYS_GLOBALOBJECT][i].oldinput.form;
		//}
		if (!window[$DYS_GLOBALOBJECT][i].addButton.disabled) {
			button_id = window[$DYS_GLOBALOBJECT][i].addButton.id;
			if (confirm(dynImp_submittext)) {
				sw = false;
				window[$DYS_GLOBALOBJECT][i].shouldNotAsk = true;
				(function (e){
					global_form_submit_lock = frm;
					MXW_DynamicObject_addButton_click(i, utility.string.getInnerText(WDG_Messages["dyn_are_you_sure_text"]));
				})();
				return false;
			} else {
				//user clicked Cancel, does not want to add this to the database, we halt
				sw = false;
				return false;
			}
		}
	}

	if (sw) {
		return true;
	}
	return false;
}

function MXW_DynamicObject_reportDone(input, isError, errorString) {
	if (typeof errorString == 'undefined') {
		errorString = '';
	}
	if (typeof(UNI_disableButtons) != 'undefined') {
		if (global_form_submit_lock) {
			UNI_disableButtons(global_form_submit_lock, /.*/, false);
		}
	}
	//unlock the form submit (KT_formSubmittalHandler)
	global_form_submit_lock = false;

	if (!isError) {
		//var obj = window[$DYS_GLOBALOBJECT][input];
		//obj.oldinput.form.submit();
	} else {
		alert("Editable Dropdown Insert Error: " + errorString);
	}
}

function MXW_DynamicObject_checkSubmit1() {
	var total = 0, complete = 0;
	for (var i in window[$DYS_GLOBALOBJECT]) {
		if (i == "openelement") {
			continue;
		}
		var obj = window[$DYS_GLOBALOBJECT][i];
		if (obj.edittype == 'E' && !obj.restrict) {
			total++;
		}
	}
	for (var i in window[$DYS_GLOBALOBJECT]) {
		if (i == "openelement") {
			continue;
		}
		var obj = window[$DYS_GLOBALOBJECT][i];
		if (obj.edittype == 'E' && !obj.restrict) {
			if (obj.iframe
				&& typeof obj.iframe.contentWindow != 'undefined'
				) {
				try {
					if (obj.iframe.contentWindow.isComplete) {
						complete++;
					}
				} catch(e) { }
			}
		}
	}
	if (complete == total) {
		window.setTimeout(function() {
			obj.oldinput.form.submit(); 
		}, 30);
	} else {
		setTimeout("MXW_DynamicObject_checkSubmit1()", 50);
		try {
			utility.dom.stopEvent(e);
		} catch(e) { }
		return false;
	}
}

function MXW_DynamicObject_updateMe() {
	if (this.sel) {
		this.sel.options.length = 0;
	}
	var masterValue = window[$DYS_GLOBALOBJECT][this.triggerObject.id].value;

	var dynMaster = window[$DYS_GLOBALOBJECT][this.triggerObject.id];
	if (dynMaster.recordset.find(dynMaster.displayfield, "=", dynMaster.edit.value)) {
		masterValue = dynMaster.recordset.Fields(dynMaster.valuefield);
	}

	this.recordset.setFilter(this.fkey, '=',  masterValue);

	if (this.recordset.RecordCount()>0) {
		this.recordset.Move(0);
		this.newvalue = this.value = this.recordset.Fields(this.valuefield);
		this.oldinput.value = this.recordset.Fields(this.valuefield);
		MXW_DynamicObject_dirtyForm(input);
		WDG_setAttributeNS(this.oldinput, 'selected', this.recordset.Fields(this.valuefield));
		this.edit.value = this.recordset.Fields(this.displayfield);
		MXW_DynamicObject_syncSelection(this.name);
	} else {
		this.newvalue = this.value = '';
		this.oldinput.value = '';
		MXW_DynamicObject_dirtyForm(input);
		WDG_setAttributeNS(this.oldinput, 'selected', '');
		this.edit.value = '';
		MXW_DynamicObject_syncSelection(this.name);
	}
	
	if (window[$DDR_MASTERSELECT_OBJ][this.name]) {
		window[$DDR_MASTERSELECT_OBJ][this.name].change();
	}
}
MXW_DynamicObject.prototype.updateMe = MXW_DynamicObject_updateMe;


function MXW_DynamicObject_updateFirst() {
	function hasParent(obj) {
		if (obj.triggerObject) {
			return window[$DYS_GLOBALOBJECT][obj.triggerObject.id];
		}
		return false;
	}

	function hasChild(test) {
		var toret = [];
		for (i in window[$DYS_GLOBALOBJECT]) {
			var obj = window[$DYS_GLOBALOBJECT][i];
			if (obj.triggerObject && obj.triggerObject.name == test.name) {
				Array_push(toret, obj);
			}
		}
		if (toret.length > 0) {
			return toret;
		} else {
			return false;
		}
	}

	function act(o, dynMaster, mode) {
		//find the foreign key for the selected value;
		if (mode == -1) {
			var keep = WDG_getAttributeNS(o.oldinput, 'selected');
			if (o.recordset.find(o.valuefield, '=', keep)) {
				var fkeyvalue = o.recordset.Fields(o.fkey);
			}
		} else {
			var keep = WDG_getAttributeNS(o.oldinput, 'selected');
			if (dynMaster.recordset.find(dynMaster.valuefield, '=', WDG_getAttributeNS(dynMaster.oldinput, 'selected'))) {
				var fkeyvalue = o.recordset.Fields(o.fkey);
			}
		}

		if (!dynMaster.updated) {
			//if the parent recordset has this key
			if (dynMaster.recordset.find(dynMaster.valuefield, "=", fkeyvalue)) {
				var pkey = dynMaster.recordset.Fields(dynMaster.valuefield);
				//set values : valuefield, displayfield on the fields. 
				dynMaster.newvalue = dynMaster.value = pkey;
				dynMaster.oldinput.value = pkey;
				WDG_setAttributeNS(dynMaster.oldinput, 'selected', pkey);
				dynMaster.edit.value = dynMaster.recordset.Fields(dynMaster.displayfield);

				if (dynMaster.triggerObject) {
					dynMaster.sel.options.length = 0;
					dynMaster.recordset.setFilter(dynMaster.fkey, '=', pkey);
					var newvalue = "";
					if (dynMaster.recordset.RecordCount()>0) {
						dynMaster.recordset.Move(0);
						newvalue = dynMaster.recordset.Fields(dynMaster.displayfield);
					}
					dynMaster.edit.value = newvalue;
					dynMaster.newvalue = dynMaster.recordset.Fields(dynMaster.valuefield);
				}

				
				MXW_DynamicObject_syncSelection(dynMaster.name, 1);
			}
		} else {
			var pkey = dynMaster.oldinput.value;
		}
		//set the this recordset key
		o.recordset.setFilter(o.fkey, '=', pkey);
		MXW_DynamicObject_syncSelection(o.name, 1);

		//re-select the initially selected field. 
		if (WDG_getAttributeNS(o.oldinput, 'selected') != null
			&& WDG_getAttributeNS(o.oldinput, 'selected') != '') {
			var keep = WDG_getAttributeNS(o.oldinput, 'selected');
		} else {
			if (o.recordset.RecordCount()) {
				var keep = o.recordset.rows[0][0];
			} else {
				var keep = -1;
			}
		}
		if (o.recordset.find(o.valuefield, "=", keep)) {
			o.newvalue = o.value = o.recordset.Fields(o.valuefield);
			o.oldinput.value = o.recordset.Fields(o.valuefield);
			WDG_setAttributeNS(o.oldinput, 'selected', o.recordset.Fields(o.valuefield));
			o.edit.value = o.recordset.Fields(o.displayfield);
			MXW_DynamicObject_syncSelection(o.name);
		} else {
			o.newvalue = o.value = '';
			o.oldinput.value = '';
			WDG_setAttributeNS(o.oldinput, 'selected', '');
			o.edit.value = '';
			MXW_DynamicObject_syncSelection(o.name);
		}
	}

	sprintf = utility.string.sprintf;

	function recursiveUpdate(o) {
		var parent = hasParent(o);
		var has_children = false;
		if (WDG_getAttributeNS(o.oldinput, 'selected') != null
			&& WDG_getAttributeNS(o.oldinput, 'selected') != '') { //has a default value
			var children = hasChild(o);
			Array_each(children, function(child) {
				has_children = true;
				if (!child.updated) {
					act(child, o, 1);
					child.updated = true;
					recursiveUpdate(child);
				}
			});
			if (parent != false && !parent.updated) {
				act(o, parent, -1);
				parent.updated = true;
				recursiveUpdate(parent);
			}
		} else {
			if (parent != false) {
				recursiveUpdate(parent)
			}
		}
		if (parent == false && has_children == false) {
			if (WDG_getAttributeNS(o.oldinput, 'selected') != null
				&& WDG_getAttributeNS(o.oldinput, 'selected') != '') {
				var keep = WDG_getAttributeNS(o.oldinput, 'selected');
			} else {
				var keep = '';
			}
			if (o.edittype == 'E') {
				if (o.recordset.find(o.valuefield, "=", keep)) {
					o.newvalue = o.value = o.recordset.Fields(o.valuefield);
					o.oldinput.value = o.recordset.Fields(o.valuefield);
					WDG_setAttributeNS(o.oldinput, 'selected', o.recordset.Fields(o.valuefield));
					o.edit.value = o.recordset.Fields(o.displayfield);
					MXW_DynamicObject_syncSelection(o.name);
				} else {
					o.newvalue = o.value = '';
					o.oldinput.value = '';
					WDG_setAttributeNS(o.oldinput, 'selected', '');
					o.edit.value = o.defaultOptionText;
					MXW_DynamicObject_syncSelection(o.name);
				}
			} else {
				o.newvalue = o.value = o.oldinput.value = keep;
				MXW_DynamicObject_syncSelection(o.name);
			}
		}
		return true;
	}

	var torecurse = [];
	for (i in window[$DYS_GLOBALOBJECT]) {
		if (i == "openelement")continue;
		var obj = window[$DYS_GLOBALOBJECT][i];
		if (typeof window[$DDR_MASTERSELECT_OBJ][obj.name] == 'undefined') {
			Array_push(torecurse, obj);
		}
	}

	Array_each(torecurse, function(item) {
		recursiveUpdate(item);
	});
	Array_each(torecurse, function(item) {
		if (!hasChild(item) && !hasParent(item)) {
			if(!WDG_getAttributeNS(item.oldinput, 'selected')) {
				item.oldinput.selectedIndex = -1;
			}
		}
	});
}

