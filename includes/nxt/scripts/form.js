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

function nxt_form_attach() {
	//[mtm]_[detail_key_value]_[cnt]
	var mtm_detail_key_re = /^mtm_(\d+)_(\d+)$/;
	//mtm_[detail_field_name]_[detail_key_value]_[record_number]
	var mtm_re = /^mtm_(.+?)_(\d+)_(\d+)$/;

	var cal_btn_re = new RegExp('_btn$', 'g');
	var edit_drop1_re = new RegExp('_edit$', 'g');
	var edit_drop2_re = new RegExp('_v$', 'g');
	var edit_drop3_re = new RegExp('_add$', 'g');
	var comma_checkbox_re = new RegExp('_commacheckbox\\d+$', 'g');
	
	// remove the min-height property from the KT_bottombuttons and KT_topbuttons class for IE7
	if (is.ie && is.v >= 7) {
          for (var d = 0; d < document.styleSheets.length; d++) {
            var imp = utility.dom.getImports(document.styleSheets[d]);
            for (var i = 0; i < imp.length; i++) {
		$nxt_alterrules = utility.dom.getRuleBySelector(imp[i], /KT_bottombuttons|KT_topbuttons/);
		for(var j=0; j<$nxt_alterrules.length; j++) {
			try {
				if ($nxt_alterrules[j].style.minHeight) {
					$nxt_alterrules[j].style.minHeight = '';
				}
			}catch(err){}
		}
            }
          }
	}

	function nxt_form_enable_details (checkbox_name) {
		var cbx = document.getElementById(checkbox_name);
		var state = !cbx.checked;
		var parts = checkbox_name.match(mtm_detail_key_re);
		var related_input_re = new RegExp("^mtm_(.+?)_" + parts[1] + "_" + parts[2] + "$", "");

		Array_each(cbx.form.elements, function(input) {
			var input_name = input.name;
			if (input_name && related_input_re.test(input_name)) {
				if (typeof(input.widget_id) == 'undefined') {
					if (input.disabled != state) {
						input.disabled = state;
					}
				} else {
					try {
						window[input.widget_type][input.widget_id].setEnabled(!state);
					} catch(err) {}
				}
			}
		});
	}
	var do_merge_down = true;
	do_merge_down = do_merge_down && typeof $NXT_FORM_SETTINGS.merge_down_value != 'undefined' && $NXT_FORM_SETTINGS.merge_down_value && !(is.ie && is.mac);
	do_merge_down = do_merge_down && typeof multiple_edits != "undefined" && multiple_edits;

	var tmp;
	var scroll = utility.dom.getPageScroll();
	var size = utility.dom.getPageInnerSize();
	tmp = utility.dom.getElementsByClassName(document, 'KT_tngform', 'DIV');
	Array_each(tmp, (function(elem){
		////////////////////
		//	STEP 0: add the float class to the div
		////////////////////
		var footer = utility.dom.getElementsByClassName(elem, 'KT_bottombuttons');
		if (footer.length == 1) {
			footer = footer[0];
			var elementList = utility.dom.getElementsByClassName(footer, 'KT_operations');
			Array_each(elementList, function(element) {
				 utility.dom.classNameAdd(element, 'KT_left');
			 });
		}
		////////////////////
		//	STEP 1:  copy footer buttons to header
		////////////////////
		if ($NXT_FORM_SETTINGS.duplicate_buttons && !(is.ie && is.mac)) {
			var footer = utility.dom.getElementsByClassName(elem, 'KT_bottombuttons', 'DIV');
			if (footer.length == 1) {
				footer = footer[0];
				var header = document.createElement('DIV');
				header.className = 'KT_topbuttons';
				header.innerHTML = footer.innerHTML;
				var tmp = footer.parentNode.insertBefore(header, footer.parentNode.firstChild);
				Array_each(['input'], function(tagname) {
					var from = footer.getElementsByTagName(tagname);
					var to = header.getElementsByTagName(tagname);
					Array_each(from, function(asd, i) {
						to[i].__eventHandlers = from[i].__eventHandlers;
						to[i].onclick = from[i].onclick;
						to[i].onmousedown = from[i].onmousedown;
						to[i].onfocus = from[i].onfocus;
					});
				});
			}
		}

		var tables = utility.dom.getElementsBySelector('div.KT_tngform table.KT_tngtable');
		do_merge_down = do_merge_down && tables.length == 1;

		var labels = utility.dom.getElementsByTagName(elem, 'label');
		var visited_labels = [];
		Array_each(labels, function(label) {
			var normal = label.htmlFor.toString().replace(/_\d+$/, '');
			var normal_re = new RegExp('^' + normal + "_\\d+$", 'g');

			var first = document.getElementById(normal+'_1');
			if (typeof first == 'undefined' || first == null || !first.tagName || first.tagName == 'undefined') {
				return;
			}
			if (first.tagName.toLowerCase() == 'input' && first.type && first.type.toLowerCase() == 'file') {
				return;
			}

			var inp = document.getElementById(label.htmlFor.toString());

			var show_cond = true;
			if (typeof inp == 'undefined' || inp == null) {
				show_cond = false;
			}
			if (show_cond && typeof inp.type != 'undefined' && inp.type != null) {
				if (inp.type.toString().toLowerCase() == 'radio') {
					show_cond = false;
				}
			}
			if (!show_cond) {
				return;
			}

			if (mtm_detail_key_re.test(label.htmlFor)) {
				// this is a detail checkbox
				// attach the onclick disable/enable detail row behavior 
				inp.onclick = function(e) {
					nxt_form_enable_details(inp.name);
				}
				if (!inp.checked) {
					nxt_form_enable_details(inp.name);
				}
			}

			if (do_merge_down) {
				if (Array_indexOf(visited_labels, normal) < 0) { // it's the first label
					Array_push(visited_labels, normal);
					var copy_down = utility.dom.createElement('input', {
						'type': 'button', 
						'className': 'merge_down', 
						'value': 'v', 
						'tabIndex': 1000
					});

					copy_down.onclick = function(e) {
						var first = document.getElementById(normal+'_1'), elements_to = [];
						var other_detail = [];
						var detail_sources = [];
						var detail_destinations = {};
						var form = first.form;
						var key = '';

						Array_each(form.elements, function(input) {
							var input_id = input.id.toString();
							try {var input_name = input.name.toString();}catch(err) {return;}
							var m_detail = input_name.match(mtm_detail_key_re);
							var m_details = input_name.match(mtm_re);
							
							if ( ((input_id.match(normal_re) && input.id != normal+'_1') || (m_detail || m_details) )
								&& !input_id.match(cal_btn_re)
								&& !input_id.match(edit_drop1_re)
								&& !input_id.match(edit_drop2_re)
								&& !input_id.match(edit_drop3_re)
								&& !input_id.match(comma_checkbox_re)
								) {
								if (m_detail) {
									//these are the mtm_[detail_key_value]_[record_number] detail checkboxes
									if (m_detail[2] == '1') {
										if (('mtm_'+ m_detail[1]) == normal) {
											detail_sources.push([m_detail[0], m_detail[1], m_detail[2]]);
										}
									} else {
										if (('mtm_'+ m_detail[1]) == normal) {
											key = 'mtm_' + m_detail[1] + '_1';
											if (typeof detail_destinations[key] == 'undefined') {
												detail_destinations[key] = [];
											}
											detail_destinations[key].push([m_detail[0], m_detail[1], m_detail[2]]);
										}
									}
								} else if (m_details) {
									//these are the other detail fields
									// mtm_[detail_field_name]_[detail_key_value]_[record_number]
									if (m_details[3] == '1') {
										if (('mtm_'+ m_details[2]) == normal) {
											detail_sources.push([m_details[0], m_details[1], m_details[2], m_details[3]]);
										}
									} else {
										if (('mtm_'+ m_details[2]) == normal) {
											key = 'mtm_' + m_details[1] + '_' + m_details[2] + '_1';
											if (typeof detail_destinations[key] == 'undefined') {
												detail_destinations[key] = [];
											}
											detail_destinations[key].push([m_details[0], m_details[1], m_details[2], m_details[3]]);
										}
									}
								} else {
									Array_push(elements_to, input);
									if (typeof detail_destinations[first.name] == 'undefined') {
										detail_destinations[first.name] = [];
									}
									detail_destinations[first.name].push([input_name]);
								}
							}
						})

						if (!/mtm_\d+/.test(normal)) {
							detail_sources = [[first.name]];
						}
						
						for (var r = 0; r < detail_sources.length; r++) {
							first = form.elements[detail_sources[r][0]];
							elements_to = detail_destinations[detail_sources[r][0]];
							if (typeof($DYS_GLOBALOBJECT) != 'undefined' && typeof (window[$DYS_GLOBALOBJECT][first.id]) != 'undefined') {
								var first_dyn = window[$DYS_GLOBALOBJECT][first.id];
							}
							if (typeof($DCC_GLOBALOBJECT) != 'undefined' && typeof (window[$DCC_GLOBALOBJECT][first.id]) != 'undefined') {
								var first_dcc = window[$DCC_GLOBALOBJECT][first.id];
							}
							if (typeof($DCM_GLOBALOBJECT) != 'undefined' && typeof (window[$DCM_GLOBALOBJECT][first.id]) != 'undefined') {
								var first_dcm = window[$DCM_GLOBALOBJECT][first.id];
								if (is.ie) {
									//the dropdown/menu somehow loose the selected options in IE 
									first_dcm.inspect();
								}
							}
							if (typeof($MMO_GLOBALOBJECT) != 'undefined' && typeof (window[$MMO_GLOBALOBJECT][first.id]) != 'undefined') {
								var first_mmo = window[$MMO_GLOBALOBJECT][first.id];
							}
	
							for (var i=0; i<elements_to.length; i++) {
								var element_to = form.elements[elements_to[i][0]];
								if (typeof($DYS_GLOBALOBJECT) != 'undefined' && typeof (window[$DYS_GLOBALOBJECT][element_to.id]) != 'undefined') {
									dyp = window[$DYS_GLOBALOBJECT][element_to.id];
									if (dyp.edittype == 'E') {
										//do not copy down values for a DynamicInput in a "add this" state
										if (first_dyn.addButton.disabled) {
											var selIndex = first_dyn.oldinput.selectedIndex;
											dyp.newvalue = first_dyn.newvalue;
											dyp.oldinput.selectedIndex = selIndex;
											dyp.oldinput.value = first_dyn.oldinput.value;
											dyp.sel.selectedIndex = selIndex;
											dyp.sel.value = first_dyn.sel.value;
											dyp.edit.value = first_dyn.edit.value;
											MXW_DynamicObject_syncSelection(element_to.id);
											dyp.addButton.disabled = true;
										}
									} else {
										dyp.oldinput.value = first_dyn.oldinput.value;
										dyp.edit.value = first_dyn.edit.value;
									}
									continue;
								}
								if (typeof($DCC_GLOBALOBJECT) != 'undefined' && typeof (window[$DCC_GLOBALOBJECT][element_to.id]) != 'undefined') {
									window[$DCC_GLOBALOBJECT][element_to.id].input.value = first_dcc.input.value;
									window[$DCC_GLOBALOBJECT][element_to.id].inspect();
								}
								if (typeof($DCM_GLOBALOBJECT) != 'undefined' && typeof (window[$DCM_GLOBALOBJECT][element_to.id]) != 'undefined') {
									window[$DCM_GLOBALOBJECT][element_to.id].input.value = first_dcm.input.value;
									window[$DCM_GLOBALOBJECT][element_to.id].inspect();
								}
								if (typeof($MMO_GLOBALOBJECT) != 'undefined' && typeof (window[$MMO_GLOBALOBJECT][element_to.id]) != 'undefined') {
									window[$MMO_GLOBALOBJECT][element_to.id].input.value = first_mmo.input.value;
									window[$MMO_GLOBALOBJECT][element_to.id].inspect();
								}
								if (first.tagName.toLowerCase() == 'input' && first.type == 'checkbox') {
									try {
										element_to.checked = first.checked;
										if (mtm_detail_key_re.test(element_to.name)) {
											nxt_form_enable_details(element_to.name);
										}
									} catch(e) { }
									continue;
								}
								if (first.tagName.toLowerCase() == 'select') {
									try { element_to.selectedIndex = first.selectedIndex; } catch(e) { }
									if (typeof $DDR_MASTERSELECT_OBJ != 'undefined' && typeof window[$DDR_MASTERSELECT_OBJ][element_to.id] != 'undefined') {
										window[$DDR_MASTERSELECT_OBJ][element_to.id].change();
									}
	
									continue;
								}
								try { element_to.value = first.value; } catch(e) { }
	
								var ktml1 = UNI_isktml(first);
								var ktml2 = UNI_isktml(element_to);
								if (ktml1 && ktml2) {
									if (ktml2.displayMode == 'RICH') {
										ktml2.setContent(ktml1.getContent());
									} else {
										ktml2.textarea.value = hndlr_load(ktml1.getContent(), "CODE");
									}
								}
							}
						}
					}

					if (typeof($CAL_GLOBALOBJECT) != "undefined" && typeof(window[$CAL_GLOBALOBJECT]) != "undefined" && typeof(window[$CAL_GLOBALOBJECT][inp.id+""])!="undefined") {
						inp = document.getElementById(inp.id + '_btn');
					}
					utility.dom.insertAfter(copy_down, inp);
				}
			}
		});
	}));
	if (is.mozilla && typeof nxt_form_ffdelayed_resizer == 'function') {
		setTimeout('nxt_form_ffdelayed_resizer()', 0);
	}
}

function nxt_form_ffdelayed_resizer() {
	var buttons = utility.dom.getElementsBySelector('div.KT_tngform input.merge_down');
	for (var i=0; i<buttons.length; i++) {
		var a = buttons[i].offsetWidth;
		buttons[i].style.width = '10px';
		buttons[i].style.width = a + 'px';
	}
}

function nxt_form_insertasnew(obj, var_name) {
	var frm = obj.form;
	if (is.ie && frm.action == '') {
		var action = window.location.href
	} else {
		var action = frm.action.toString();
	}
	parts = action.split("?");
	var qs = new QueryString(parts[1]); var new_qs = [];
	var re = new RegExp('^'+var_name, 'g');
	Array_each(qs.keys, function(key, i) {
		if (! key.match(re)) {
			Array_push(new_qs, key+'='+qs.values[i]);
		}
	});
	var new_part = new_qs.join('&');
	action = parts[0];
	if (new_part != '')
		action += '?' + new_part;
	frm.action = action;
	return true;
}

utility.dom.attachEvent2(window, 'onload', nxt_form_attach);

