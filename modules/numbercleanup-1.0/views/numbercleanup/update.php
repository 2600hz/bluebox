<style>
.selected {
        color:blue;
	cursor: move;
}
td, th{
        padding: 5px;
}
td.noborder {
	border: 0px;
	vertical-align: top;
}
.dragme {
	cursor: move;
}
</style>

<?php echo form::open_section('Clean Numbers'); ?>
<span style="float: right"><button class="small_green_button button" onclick="numberclean_popover(null); return false;">New Rule</button></span>

<table id=numbercleantable border=1>
	<tr><th>Search Field</th><th>Pattern</th><th>Update Field</th><th>Value</th></tr>
</table><br>
Drag and drop to re-order routes
<div id="edit_numberclean" style="position: absolute; top: 0px; left: 0px; right: 0px; bottom: 0px; display: none;">
<div style="position: absolute; top: 0%; left: 0%; background-color: gray; display: table-cell; vertical-align: middle; opacity:0.8; filter:alpha(opacity=80); width:100%; height:100%"></div>
<div style="position: relative; top: 50%; left: 50%; width: 49%; height: 49%;">
<div style="position: relative; top: -10em; left: -15em; width: 30em; height: 18em; border: 3px blue solid; background-color: white; padding: 10px;">
	<table style="width: 100%; height: 100%; ">
		<tr><td>Search Field</td><td><select id=numberclean_searchfieldselect>
			<option value="destination_number">Destination Number</option>
			<option value="effective_caller_id_name">Caller Number</option>
			<option value="effective_caller_id_name">Caller Name</option>
		</select></td></tr>
		<tr id="numberclean_regex"><td>Pattern (Regular Expression)</td><td><input id=numberclean_pattern></td></tr>
		<tr><td>Update Field</td><td><select id=numberclean_replacefieldselect>
			<option value="destination_number">Destination Number</option>
			<option value="effective_caller_id_name">Caller Number</option>
			<option value="effective_caller_id_name">Caller Name</option>
		</select></td></tr>
		<tr><td>Replacement value</td><td><input id=numberclean_newvalue></td></tr>
		<tr><td colspan=2>Hint: if the regular expression contains brackets, the replacement value can contain $1</td></tr>
		<tr><td colspan=2>
		<span style="float: right">&nbsp;<button class="small_red_button button" onclick="document.getElementById('edit_numberclean').style.display='none'; return false;" >Cancel</button></span>
		<span id="numberclean_buttons_for_edit">
			<span style="float: right">&nbsp;<button class="small_red_button button" onclick="return numberclean_erase(editing);">Delete</button></span>
			<span style="float: right">&nbsp;<button class="small_green_button button" onclick="return numberclean_update_entry(editing);">Update</button></span>
		</span>
		<span id="numberclean_buttons_for_add">
			<span style="float: right">&nbsp;<button class="small_green_button button" onclick="return numberclean_update_entry(null);">Add</button></span>
		</span>
		</td></tr>
	</table>
</div>
</div>
</div>
<script>
editing=null;
dragging=null;
oldclass=null;
moved=0;

function numberclean_popover(row) {
	if (row===null) {
		document.getElementById("numberclean_searchfieldselect").value="destination_number";
		document.getElementById("numberclean_pattern").value="";
		document.getElementById("numberclean_replacefieldselect").value="destination_number";
		document.getElementById("numberclean_newvalue").value="$1";
		document.getElementById("numberclean_buttons_for_edit").style.display="none";
		document.getElementById("numberclean_buttons_for_add").style.display="inline";
	} else {
		var inputs=row.getElementsByTagName("input");
		for (var index=0; index<inputs.length; index++) {
			input=inputs.item(index);
			document.getElementById(input.name.match(/([^\[]*)\]$/)[1]).value=input.value;
		}
		document.getElementById("numberclean_buttons_for_edit").style.display="inline";
		document.getElementById("numberclean_buttons_for_add").style.display="none";
	}
	editing=row;
	document.getElementById('edit_numberclean').style.display='block'; 
	return false;
}
function numberclean_erase(row) {
	document.getElementById('edit_numberclean').style.display='none';
	row.parentNode.deleteRow(row.rowIndex);
	return false;
}
function numberclean_update_entry(row) {
	var fields=new Array("searchfieldselect","pattern","replacefieldselect","newvalue");
	var newentry=new Array();
	for (var index=0; index<fields.length; index++) {
		newentry[index]=document.getElementById("numberclean_"+fields[index]);
	}
	numberclean_update_row(row,newentry);
	numberclean_renumber_fields();
	document.getElementById('edit_numberclean').style.display='none';
	return false;
}
function numberclean_update_row(row,rowdata) {
	var nct=document.getElementById("numbercleantable");
	var doinsertlater;
	if (row==null) {
		row=document.createElement("tr");
		doinsertlater=1;
	} else {
		doinsertlater=0;
	}
	row.innerHTML="";
	row.className="dragme";
	row.onmousedown=function () {
		if (editing!=null) {
			editing.className=oldclass;
		}
		dragging=row;
		oldclass=dragging.className;
		dragging.className="selected";
		moved=0;
		return false;
	}

	row.onmousemove=function () {
		if ((dragging==row) || (dragging==null)) {
			return;
		}
		moved=1;
                if (dragging.rowIndex > row.rowIndex) {
                        row.parentNode.insertBefore(dragging,row);
                } else {
                        row.parentNode.insertBefore(dragging,row);
                        row.parentNode.insertBefore(row,dragging);
                }
		return false;
	}

	row.onclick=function () {
		if (moved==0) {
			numberclean_popover(row);
		}
	}
	for (var i=0; i<rowdata.length; i++) {
		numberclean_setcell(row,i,rowdata[i]);
	}

	if (doinsertlater==1) {
		nct.appendChild(row);
	}
	return row;
}

function numberclean_setcell(row,position,source) {
	var cell=row.cells[position];
	if (!cell) {
		cell=document.createElement("td");
		row.appendChild(cell);
	}
	var value;
	var caption;
	if (source instanceof HTMLSelectElement) {
		caption=source.options.item(source.selectedIndex).label;
		value=source.value;
		id=source.id;
	} else if (source instanceof HTMLInputElement) {
		caption=source.value;
		value=source.value;
		id=source.id;
	} else {
		value="A";
		caption="B";
		id="C";
		alert(source);
	}
	cell.innerHTML="<input type=hidden name='numberclean[0]["+id+"]' value="+value+">"+caption;
}

// this takes all the inputs in the table, and replaces whatever is between the first [] with
// the number of the row - 1 (-1 because the header is row 0, so we want the first line of data, row 1,
// to be index 0)
function numberclean_renumber_fields () {
	var inputs=document.getElementById("numbercleantable").getElementsByTagName("input");;
	for (var input=0; input<inputs.length; input++) {
		var n=inputs[input].name;
		n=n.substr(0,n.indexOf('[')+1)+
			(inputs[input].parentNode.parentNode.rowIndex-1).toString()+
			n.substr(n.indexOf(']'));
		inputs[input].name=n;
	}
}
document.onmouseup = function () {
	dragging.className=oldclass;
	dragging=null;
}

// Fill in the grid. do it as if the fields were keyed in manually - it's easier that way.
setup=<?php print $numberclean; ?>;
for (var loop=0; loop<setup.length; loop++) {
	for (var i in setup[loop]) {
		document.getElementById(i).value=setup[loop][i];
	}
	numberclean_update_entry(null);
}
</script>

<?php echo form::close_section(); ?>



