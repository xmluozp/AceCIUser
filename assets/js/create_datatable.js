/*
*  Used for create datatable
*
*  data-source: --- what column looking for
*  data-orderby-desc
*  data-orderby-asc: --- if has this attribute, will be as default sorting,
*  						 the value is priority when there are more than 1 sorting columns
*  data-filter: ---filter by which columns
*  data-icon: --- what boolean should display
*  data-class: --- change the column's classname
*  data-render: --- change content, render function should be fn(data, type, row, meta)
*  data-multiselect: --- set the column as multi checkbox column
*
* */

// settings of iconlist matches of value
var iconList={
	"boolean":{
			"0" : "block",
			"1" : "check"
		}
};

function reload_dataTable(selector)
{
	$(selector).DataTable().ajax.reload(null, false).draw();
}

function create_dataTable(selector, url, searchForm, lastColumn)
{
	count = 0;

	// determine what columns are using filter
	filters = [];

	// binding data with columns
	columns = [];

	// sort
	orderBy = [];

	// datatable
	datatable = null;

	//icons = [];
	// get data source from columns
	$(selector + " thead tr th").each(
		function () {

			ds_item={};
			ft_item = "";
			order_item = {};

			// get column name from table, passing to remote AJAX
			if($(this).attr("data-source") != undefined)
			{
				ds_item["data"] = $(this).attr("data-source");

				// set this column work with filter
				if($(this).attr("data-filter") != undefined)
				{
					ft_item = $(this).attr("data-source");
					filters.push(ft_item);
				}

				if($(this).attr("data-orderby-desc") != undefined)
				{
					priority = $(this).attr("data-orderby-desc");

					order_item = [columns.length, "desc", priority];
					orderBy.push(order_item);
				}else if($(this).attr("data-orderby-asc") != undefined)
				{
					priority = $(this).attr("data-orderby-asc");

					order_item = [columns.length, "asc", priority];
					orderBy.push(order_item);
				}

				// when reading data, it will be an icon
				if($(this).attr("data-icon") != undefined)
				{
					icontype=$(this).attr("data-icon");

					ds_item["render"] = function(data, type, row, meta){
						iconName = iconList[this.icontype][data];
						return "<i class='material-icons ico_" + iconName + "'>"+iconName+"</i>"
					};
				}

				if($(this).attr("data-class") != undefined)
				{
					ds_item["className"] = $(this).attr("data-class");
				}

				if($(this).attr("data-toolbar") != undefined)
				{
					ds_item["orderable"] = false;
					ds_item["defaultContent"] = lastColumn;
				}

				if($(this).attr("data-multiselect") != undefined)
				{
					ds_item["orderable"] = false;
					ds_item["checkboxes"] = {'selectRow': true};
				}

				if($(this).attr("data-render") != undefined)
				{
					renderName = $(this).attr("data-render");

					if(typeof window[renderName] == "function") {
						ds_item["render"] = window[renderName];
					}
					/*function(data, type, row, meta){
						//window[$(inputItem).attr("data-render")](inputItem, value);
						if(typeof window[renderName] == "function") {
							return window[renderName](data, type, row, meta);
						}
					}*/
				}

				columns.push(ds_item);
			}
		}
	);

	orderBy.sort(sortFunction);

	function sortFunction(a, b) {
		if (a[2] === b[2]) {
			return 0;
		}
		else {
			return (a[2] < b[2]) ? -1 : 1;
		}
	}

	if(orderBy.length == 0)
	{
		orderBy = [[ 0, "desc" ]];
	}

	/*
	// / last column, which is tools column
	toolButtons = {
		"data":null,
		"className": "edit_column",
		"orderable": false,
		"defaultContent" : lastColumn,
	};

	// edit column
	columns.push(toolButtons);*/

	// disable the error alert
	$.fn.dataTable.ext.errMode = 'none';
	$.fn.dataTable.ext.classes.sFilterInput = 'form-control';
	$.fn.dataTable.ext.classes.sLengthSelect = 'select-inline';
//	$.fn.dataTable.ext.classes.sPaging ='pagination pagination-sm ' + $.fn.dataTable.ext.classes.sPaging;


	// initialize the datatable
	datatable = $(selector).DataTable( {
		"processing": true, // the processing bar
		"serverSide": true,	// run ajax
		"autoWidth": false,
		"lengthMenu": [[10, 25, 50, 0], [10, 25, 50, "All"]],
		"order": orderBy,
		"ajax": {
			url: url,
			data: function (d) {
				if ($.isFunction(fnGetSearch)) {
					d.extraSearch = fnGetSearch();
				}else {
					d.extraSearch = Array(null);
				}

				d.filters = filters;
			}
		},
		"columns": columns,
		'select': {
			'style': 'multi'
		},
		"language": {
			search: "_INPUT_",
			searchPlaceholder: "Filter..."
		}
	} ).on('xhr.dt', function ( e, settings, json, xhr ) { // error handling

		// if get error,
		if(json == null)
		{
			// print all messages on screen
			$("html").html(xhr.responseText);
		}
	} )	;

	//$(".dataTables_filter").hide();

	$(searchForm).submit(function( event ) {

		$(selector).DataTable().ajax.reload();
		event.preventDefault();
	});

	// submit the multi select
	this.multi_select = function(column, url, callback){

		var rows_selected  = datatable.column(column).checkboxes.selected();

		var form_id = selector + "_form";
		var array = [];
		$.each(rows_selected, function(index, rowId){

			array.push(rowId);
		});

		$.ajax({
			type: "POST",
			url: url,
			data: {"selected": array},
			success: function(data)
			{
				if(typeof window[callback] == "function") {
					window[callback](data);
				}
			}
		}).fail(function(data){
			$("html").html(data.responseText);
		});

	}

	// function of passing extra search's variables
	function fnGetSearch()
	{
		searchInfomation = {};
		formJSON = $(searchForm).serializeArray();

		for(var obj in formJSON)
		{
			searchInfomation[formJSON[obj].name] = formJSON[obj].value;
		}

		return searchInfomation;
	}
}


