/*The MIT License (MIT)

Copyright (c) 2015 Paul Zepernick

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/


/**
 * @author Paul Zepernick http://www.paulzepernick.com
 * @summary DtColSearch
 * @description Add Column Search fields in the header or footer of a DataTable
 * 
 */
(function(window, document, undefined) {
	var factory = function( $, DataTable ) {
		"use strict";
		
		
		
		/**
		 * Function: DtColSearch
		 * Purpose: Automatically create text boxes and selects for column based server side searching
		 * Returns: object: DtColSearch - must be called with 'new'
		 * Inputs:   mixed:mTable - target table
		 *  @param {object} dt DataTables instance or HTML table node. With DataTables
		 *    	1.10 this can also be a jQuery collection (with just a single table in its
		 *    result set), a jQuery selector, DataTables API instance or settings
		 *    	object.
		 *  @param {object} Initialization settings, with the following properties
		 *  			string:placement - 'head', 'foot' (default 'head')
		 *  			bool:placeholders - Add placeholders to the search inputs? (default true)
		 *  			string:controlClass - Class / Classes to apply to each control
		 *  			array:select - select settings object
		 *  					mixed:name - String or int referencing the column data string, column name, or column index
		 *  					mixed:options - String Array.  The value / display of the option can be separated with a | char.  Elements with no pipe will use the same value for the option value and display
		 *  									OR
		 *  									function(jqSelObj) jQuery object of the select being created is passed in.  This can then have the options appended to it.
		 *  					bool:header - Should a header entry be generated for the select? (default true)  
		 * 
		 * The following properties are read from the DataTable settings:
		 * 		bool:columns.searchable - used to determine if the column should receive a input for searching
		 * 		int:searchDelay - used to determine the amount of time to wait before not receiving user input for searching.
         * 	    	Default is 500 if not specified
		 * 
		 */
		var DtColSearch = function(dt, opts) {
			if ( ! this instanceof DtColSearch ) {
				alert( "DtColSearch warning: DtColSearch must be initialised with the 'new' keyword." );
				return;
			}
			
			var defaults = {
					placement: "head",

					select: [],
					
					placeholders: true,
					
					controlClass: "form-control"
			};
			
			var mergedOpts = $.extend({}, defaults, opts);
			
			//return the options for this plugin
			this.getOptions = function() {
				return mergedOpts;
			};
			
			var selectDefaults = {
					header: true
			};
			//default options describing a select
			this.getSelectDefaults = function() {
				return selectDefaults;
			};
			
			this._delayCall = (function(){
				  var timer = 0;
				  return function(ms, callback){
				    clearTimeout (timer);
				    timer = setTimeout(callback, ms);
				  };
				})();
			
			var dtsettings = $.fn.dataTable.Api ?
					new $.fn.dataTable.Api( dt ).settings()[0] :
						dt.fnSettings();
			
			this.init(dt, dtsettings, mergedOpts);
		};
		
		
		
		DtColSearch.prototype = {
			dtapi : {},
			dtsettings: {},
			
			//init the extension and build the search fields
			init : function(dtapi, dtsettings, opts) {
				
				if(dtsettings === undefined || dtsettings == null) {
					// most likely a bad selector for the table being referenced.
					
					return;
				}
				
				if(this.getOptions().placement !== "head" && this.getOptions().placement !== "foot") {
					alert("[DtColSearch] placement option must be one of these ['head', 'foot']");
					return;
				}
				
				dtsettings.searchDelay = dtsettings.searchDelay || 500;
				this.dtapi = dtapi;
				this.dtsettings = dtsettings;
				//console.log(dtsettings);
								
				var tr = $("<tr/>").addClass("dataTable_colSearchBar");
				
				var cols = dtsettings.aoColumns;
				var colLen = cols.length;
				for(var i = 0; i < colLen; i++) {
				//	console.log(cols[i]);
					if(cols[i].bVisible === false) {
						continue;
					} else if(cols[i].bSearchable === false || cols[i].className === 'control') {
						tr.append($("<td/>").toggle(cols[i].className !== 'control'));
						continue;
					} 
						
					var name = cols[i].data || cols[i].mData;
					var input = this._getSearchCtrl(name, i).addClass(this.getOptions().controlClass);
					var td = $("<td/>").append(input);
					tr.append(td);
					
				}
				
				var thisPlugin = this;
				
				//console.log(dtapi.tables().nodes().to$().length);
				
				// loop all tables contained in the DataTable api context
			//	console.log(dtapi.tables().toArray());
				dtapi.tables().nodes().to$().each(function() {
					
					
					var dt = $(this).DataTable();
					
					var table = dt.table().to$();
					//console.log(table);
					var header = dt.header().to$();
					//console.log(header);
					var footer = dt.footer().to$();
					//console.log(footer);
					var trClone = tr.clone(true, true);
					
					var parent;
					if(thisPlugin.getOptions().placement === "head") {
							parent = header;
					} else {
						parent = footer;
						//build a footer if the table does not have one
						if(parent.length === 0) {
							parent = $("<tfoot/>");
							table.append(parent);
						}
					}
								
					//console.log(parent);
					parent.prepend(trClone);
					//console.log("ran prepend....");
					
					dt.on("responsive-resize.dt", function(e, dtResized, visibleArray) {
						//console.log("responsive resize event trigger");
						//console.log(visibleArray);
						
						for(var i = 0; i < visibleArray.length; i++) {
							//console.log("col[" + i + "] " + api.column(i).visible());
							trClone.children().eq(i).toggle(visibleArray[i]);
						}
						
					});
					
					
				});
				
						
			},
			
			//private method to build the text or select box for the searching
			_getSearchCtrl : function(name, index) {
				
				var selects = this.getOptions().select;
				var len = selects.length;
				for(var i = 0; i < len; i++) {
					var s = $.extend({}, this.getSelectDefaults(), selects[i]);
					if(s.name === name || s.name === index) {
						return this._buildSelect(index, s);
					}
				}
				
				return this._buildText(index);
			},
			
			//private method to build a select control and hook a change listener to it
			_buildSelect : function(index, sObj) {
				var plugin = this;
				var select = $("<select/>");
				if($.isFunction(sObj.options)) {
					sObj.options(select);
				} else {
					this._appendOptsToSelect(select, sObj.options);
				}
				
				if(sObj.header === true) {
					var hTxt = $(plugin.dtapi.column(index).header()).text();
					select.prepend($("<option/>").val("").text("--" + hTxt + " Search " + "--")).val("");
				}
				
				select.change(function() {
					plugin._doSearch($(this), index);
				});
				
				return select;
			},
			
			_appendOptsToSelect: function(jqSel, optsArray) {
				var len = optsArray.length;
				for(var i = 0; i < len; i++) {
					var spl = optsArray[i].split("|");
					var val = spl[0];
					var display = spl.length > 1 ? spl[1] : val;
					jqSel.append($("<option/>").val(val).text(display));
				}
			},
			
			_buildText : function(index) {
				var plugin = this;
				var input = $("<input/>")
					.attr("type", "text")
					.keyup(function(event) {
							var input = $(this);
							var key = event.which;
							if(key !== 9) {
								// don't run for the tab key.  It cancels the search out and we leave the field
								plugin._delayCall(plugin.dtsettings.searchDelay, function() {
									plugin._doSearch(input, index);
								});
							}
						});
				
				if(plugin.getOptions().placeholders === true) {
					var hTxt = $(plugin.dtapi.column(index).header()).text();
					input.attr("placeholder", hTxt);
				}
				
				return input;
			},
			
			_doSearch : function(input, index) {
				//console.log(input);
				this.dtapi
					.columns(index)
					.search(input.val())
					.draw();
			}
		};
		
		
		$.fn.dataTable.DtColSearch = DtColSearch;
		$.fn.DataTable.DtColSearch = DtColSearch;

		 //backward compat
	    $.fn.dataTable.DtServerColSearch = function(dt, opts) {
	    	return new DtColSearch(dt, opts);
	    };
	    $.fn.DataTable.DtServerColSearch = function(dt, opts) {
	    	return new DtColSearch(dt, opts);
	    };
	    
		
		
		return DtColSearch;
	};


	
	// Define as an AMD module if possible
	if ( typeof define === 'function' && define.amd ) {
		define( ['jquery', 'datatables'], factory );
	}
	else if ( typeof exports === 'object' ) {
	    // Node/CommonJS
	    factory( require('jquery'), require('datatables') );
	}
	else if ( jQuery && !jQuery.fn.dataTable.DtColSearch ) {
		// Otherwise simply initialize as normal, stopping multiple evaluation
		factory( jQuery, jQuery.fn.dataTable );
	}
	
		
	
})(window, document);