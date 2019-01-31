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

$ICT_MAIN_CLASSNAME = 'NumericInput';

$ICT_DEFAULT_MIN = -999999999999999999;
$ICT_DEFAULT_MAX = 999999999999999999;

//time after mousedown when to change the tick setting, new step value, new step timeout
//this array should contain at least one row, the initial settings
$ICT_TICK_INCREMENT = [
	[0, 1, 60]
	,[2000, 10, 100]
//	,[6000, 10, 100]
];

$ICT_DIVPREFIX = 'ict_explanation_div_';

$ICT_VISUAL_ALERT_DIV_CLASSNAME = 'MXW_ICT_visual_alert_div';
$ICT_VISUAL_ALERT_INPUT_CLASSNAME = 'MXW_ICT_visual_alert_input';

$ICT_GLOBALOBJECT = "NumericInputs";
if (typeof window[$ICT_GLOBALOBJECT] == 'undefined') {
	window[$ICT_GLOBALOBJECT] = {};
}


function MXW_NumericInput (input) {
	if (is.safari && is.version < 1.4) {
		return;
	}
	var originalElement = document.getElementById(input);
	if (typeof(originalElement.kt_uni_attached) == 'undefined') {
		originalElement.kt_uni_attached = true;
	} else {
		return;
	}

	this.name = input;
	this.input = originalElement;
	this.input.widget_id = this.name;
	this.input.widget_type = $ICT_GLOBALOBJECT;
	this.mousedown = false;
	if (is.mozilla) {
		this.input.disableAutocomplete = true;
	} else {
		this.input.autocomplete = "off";
	}
	this.input.className = (this.input.className?this.input.className + ' ':'') + $ICT_MAIN_CLASSNAME;

	this.spinner = (WDG_getAttributeNS(this.input, 'spinner')+'').toLowerCase()=="yes";

	this.allowNegatives = (WDG_getAttributeNS(this.input, 'negatives')+'').toLowerCase()=="yes";
	this.allowFloat = (WDG_getAttributeNS(this.input, 'floats')+'').toLowerCase()=="yes";
	this.parseFunc = this.allowFloat?parseFloat:parseInt;
	
	this.strikes = 0;

	var div = utility.dom.createElement('div', {
		'id': $ICT_DIVPREFIX + this.name, 
		'style': 'position: absolute; display: none; ', 
		'className': $ICT_VISUAL_ALERT_DIV_CLASSNAME
	});
	div.innerHTML = WDG_Messages["the_format_is"];
	if (this.allowFloat) {
		div.innerHTML += WDG_Messages["also_floats"];
	}
	if (this.allowNegatives) {
		div.innerHTML += WDG_Messages["also_negatives"];
	}
	this.div = document.body.appendChild(div);
	this.div.style.display = 'none';
	this.div.style.position = 'absolute';

	var minValue = this.parseFunc(WDG_getAttributeNS(this.input, 'min'), 10);
	this.minValue = this.allowNegatives?(!isNaN(minValue) ? minValue : null):(!isNaN(minValue) ? minValue : 0);
	//_t(Number.MIN_VALUE);
	var maxValue = this.parseFunc(WDG_getAttributeNS(this.input, 'max'), 10);
	this.maxValue = !isNaN(maxValue)?maxValue:null;

	if (this.minValue> this.maxValue) {
		//what then?
	}

	// full regexp - /^-?[0-9]*\.?[0-9]*$/
	var rx = "^";
	if(this.allowNegatives) {
		rx += "-?";
	}
	rx += "[0-9]*";
	if(this.allowFloat) {
		rx += "\\.?[0-9]*";
	}
	rx += "$";
	this.input.numeric_regexp = new RegExp(rx);

	var obj = this;
	utility.dom.attachEvent_base(obj.input, is.mozilla?"keypress":"keydown", function (e){return MXW_NumericInput_keydownhandler(obj, e);}, 1, false, false);
	utility.dom.attachEvent_base(obj.input, "keyup", function (e){return MXW_NumericInput_keyhandler(obj, e);}, 1, false, false);
	utility.dom.attachEvent_base(obj.input, "blur", function (e){return MXW_NumericInput_blurhandler(obj, e);}, 1, false, false);
	utility.dom.attachEvent_base(obj.input, "focus", function (e){return MXW_NumericInput_focus(obj, e);}, 1, false, false);

	this.spinner = new MXW_Spin(this, $ICT_TICK_INCREMENT, this.spinner);

	window[$ICT_GLOBALOBJECT][input] = this;
	this.setEnabled(!this.input.disabled);
	try {Kore.addUnloadListener(this.dispose, this);}catch(err){}
	return this;
}

MXW_NumericInput_dispose = function() {
	try {Kore.removeUnloadListener(this.dispose, this);} catch(err) {}
	try {this.div.parentNode.removeChild(this.div);}catch(err) {}
	try {this.spinner.container.parentNode.removeChild(this.spinner.container);}catch(err) {}
	try{delete window[$ICT_GLOBALOBJECT][this.name];}catch(err){}
}
MXW_NumericInput.prototype.dispose = MXW_NumericInput_dispose;

function MXW_NumericInput_setEnabled(state) {
	this.input.disabled = !state;
	this.spinner.setEnabled(state);
}
MXW_NumericInput.prototype.setEnabled = MXW_NumericInput_setEnabled;

function MXW_NumericInput_focus(obj, evt){
	obj.kt_focused = true;

	this.maskDirty = false;
	if(!obj.validate() && obj.input.value != '') {
		obj.spin(1, 0, evt);
	}
}

function MXW_NumericInput_formhandler(input, evt) {
	var obj = window[$ICT_GLOBALOBJECT][input];
	if (!obj.input.value.match(obj.input.numeric_regexp)) {
		MXW_visualAlert(obj, 1, 'ICT');
		try {
			obj.input.focus();
		} catch(e) { }
		return false;
	} else {
		MXW_visualAlert(obj, 0, 'ICT');
		return true;
	}

}

function MXW_NumericInput_blurhandler(obj, evt) {
	obj.kt_focused = false;
	MXW_visualAlert(obj, 0, 'ICT');
	if (is.mozilla) {
		if (
			(evt.explicitOriginalTarget.id!=obj.input.id+"_decbutton" && evt.explicitOriginalTarget.id!=obj.input.id+"_incbutton" )
			) {
			spin_stop(evt);
		}
	} else if (is.ie){
		if (!obj.mousedown) {
			spin_stop(evt);
		}
	} else {
		if (!obj.mousedown) {
			spin_stop(evt);
		}
	}
	if (obj.input.value != '') {
		obj.spin(1, 0, evt);
	}
	if (obj && obj.maskDirty) {
		obj.maskDirty = false;
		if (obj.input.fireEvent) {
			obj.input.fireEvent("onchange");
		} else if(document.createEvent){
			try{
				//this try is for Opera 7.5
				var me = document.createEvent("Events");
				me.initEvent('change', 0, 0);
				obj.input.dispatchEvent(me);
			}catch(err){}
		}
	}
}
function MXW_NumericInput_keydownhandler(obj, evt) {
	if (!obj.kt_focused) {
		utility.dom.stopEvent(evt);
		return false;
	}

	var myevnt = utility.dom.setEventVars(evt);
	var kc = is.mozilla?evt.charCode:evt.keyCode;
	var mkc = is.mozilla?evt.keyCode:0;

	if (
		(is.mozilla) && (kc==45 || kc==43 || mkc==40 || mkc==38 || kc==61) 
		|| 
		(is.ie || is.opera) && (kc==38 || kc==40 || kc==107 || kc==109 || kc==187 || kc==189) 
	) {
		if (!window[$SPN_GLOBALOBJECT]['timeout']) {
			var cmp = 1;

			if (obj.input.createTextRange && !is.opera) {
				if (is.windows) {
					var rngSel = document.selection.createRange();
					var rngTxt = obj.input.createTextRange();
					cmp = rngSel.compareEndPoints("StartToStart", rngTxt);
				} else if(is.mac) {
					cmp = 1;
				} else {
					cmp = 1;
				}
			} else if (obj.input.setSelectionRange) {
				cmp = obj.input.selectionStart;
			}
			if (cmp==0 && !(is.mozilla && (mkc==40 || mkc==38) || (is.ie || is.opera) && (kc==38 || kc==40)) ) {
				return true;
			}
			utility.dom.stopEvent(myevnt.e);
			var direction = (
				is.mozilla && (kc==43 || mkc==38 || kc==61) 
				|| 
				(is.ie || is.opera) && (kc==38 || kc==107 || kc==187)
				)
				?
				1:-1;
			obj.spin(direction, 1, evt);
			spin_start(obj, direction);
		}
		utility.dom.stopEvent(myevnt.e);
		return false;
	}
}
function MXW_NumericInput_keyhandler(obj, evt) {
	if (!obj.kt_focused) {
		utility.dom.stopEvent(evt);
		return false;
	}

	if (!obj.input.value.match(obj.input.numeric_regexp)) {
		if (obj.input.lastMatched) {
			obj.input.value = obj.input.lastMatched;
		} else {
			obj.input.value = "";
		}
		obj.strikes++;
	} else {
		if (obj.input.value!="" && !obj.input.value.match(/\.\d*$/) && obj.input.value!="-") {
			//obj.spin(1, 0, evt);
		}
		MXW_visualAlert(obj, 0, 'ICT');	
		obj.input.lastMatched = obj.input.value;
	}
	if (obj.strikes >= 3) {
		MXW_visualAlert(obj, 1, 'ICT');	
		obj.strikes = 3;
	}
	spin_stop(evt);
}

MXW_NumericInput.prototype.validate = function () {
	var v = this.parseFunc(this.input.value);
	return !isNaN(v);
}

function MXW_NumericInput_spin(direction, step, e) {
	if (this.input.value == '') {
		this.input.value = this.minValue==null?0:this.minValue;
	}

	spin_stop(e);
	if (typeof step=="undefined") {
		step = this.spinner.ticker[0][1];
	}

	step = direction * step;
	var oldVal = this.input.value;
	var newValue = "";
	if (this.validate()) {
		var currentvalue = this.parseFunc(this.input.value);
		newValue = currentvalue + step;

		var curDecimals = getFractPartLength(currentvalue);
		var addDecimals = getFractPartLength(step);
		var newDecimals = getFractPartLength(newValue);

		var maxNewFractLength = Math.max(curDecimals, addDecimals);
		if (newDecimals>maxNewFractLength) {
			newValue = newValue * Math.pow(10, maxNewFractLength);
			newValue = Math.round(newValue);
			newValue = newValue/Math.pow(10, maxNewFractLength);
		}

		if ( 
				( this.minValue==null || this.minValue!=null && (this.minValue < newValue))
			&&
				( this.maxValue==null || this.maxValue!=null && (newValue < this.maxValue) )
			) {
		} else {
			newValue = (this.minValue >= newValue)?this.minValue:this.maxValue
		}
	} else {
		if (step!=0) {
			var newValue = this.minValue==null?0:this.minValue;
		} else {
			var newValue = "";
		}
	}
	if (oldVal != newValue.toString()) {
		//find current cursor position (selection)
		var selStart = MXW_getSelectionStart(this.input);
		var selEnd = MXW_getSelectionEnd(this.input);
		this.input.lastMatched = this.input.value = newValue;
		//...and restore the old selection

		if(e && e.button != 1) {
			MXW_setSelectionRange(this.input, selStart, selEnd);
		}
		this.maskDirty = true;
	}
}
MXW_NumericInput.prototype.spin = MXW_NumericInput_spin;

function getFractPartLength(s) {
		s = s.toString().replace(/(^\d*\.)/i, '');
		return s.length;
}
