<link rel="stylesheet" type="text/css" href="/assets/css/jquery/smoothness/ui.all.css" media="screen" />
<script type="text/javascript" src="/assets/js/jquery/ui.accordion.js"></script>
<script type="text/javascript" src="/assets/js/jquery/jquery.dependent.js"></script>
<script type="text/javascript" src="/assets/js/jquery/ui.tabs.js"></script>
<script type="text/javascript" src="/assets/js/mustache.js"></script>

<div id="endpoint_update_header" class="txt-center update endpoint module_header">
	<h2><?php echo $title; ?></h2>
</div>

<div id="endpoint_update_form" class="update endpoint">
<script>
	oui=<?php echo $oui_json; ?>;
	models=<?php echo $models_json; ?>;
	function update_models(brand) {
		document.getElementById('model_select').innerHTML="<option value=''>Select</option>"+models[brand];
	}
	function check_oui(input) {
		// Convert mac to caps, remove non-mac characters (e.g. :), and get the first 6 characters - the OUI.
		brand=oui[input.value.toUpperCase().replace(/[^\dA-F]/g,'').substr(0,6)];
		select=document.getElementById('brand_select');
		if ((brand!=null) && (brand!=select.value)) {
			select.value=brand;
			update_models(brand);
		}
	}
	function update_model() {
		document.getElementById('dontsave_hidden').value='true';
		form=document.getElementById('endpointmanager_edit');
		if (form==null) {
			form=document.getElementById('endpointmanager_create');
		}
		form.submit();
	}
	function display_button_subdiv(button) {
		var search_off="buttons_"+button+"_type_";
		var search_on=search_off+document.getElementById("buttons_"+button+"_type").value;
		var divs=document.getElementsByTagName('div');
		for (var i=divs.length; i--;) {
			var div=divs.item(i);
			if (div.id.indexOf(search_on)==0) {
				div.style.display="block";
			} else if (div.id.indexOf(search_off)==0) {
				div.style.display="none";
			}
		}
	}
	function change_line(lineno) {
		var label=document.getElementById("lines_"+lineno+"_sip");
		if (label.selectedIndex==0) {
			label="Line "+lineno+" (unused)";
		} else {
			label="Line "+lineno+": "+label.options.item(label.selectedIndex).label;
		}
		var dropdowns=document.getElementsByTagName("select");
		for (var i=dropdowns.length-1; i--;) {
			if ((dropdowns.item(i).id.indexOf("buttons_")==0) && (dropdowns.item(i).id.indexOf("_sipaccount")>0)) {
				dropdowns.item(i).options.item(lineno-1).text=label;
			}
		}
		for (var i=3; i--;) {
			var select=document.getElementById("buttons_"+i+"_sipaccount");
//			select.options.item(lineno-1).text=label;
		}
	}
</script>

	<?php echo form::open(); ?>
	<?php echo form::open_section('Endpoint information'); ?>
	<?php echo form::hidden('dontsave','false'); ?>

	<div class="field">
	<?php 
		echo form::label(array(
			'for'=>'endpoint[name]',
			'hint'=>'Nickname for this endpoint',
			'help'=>'This is a friendly nickname for your endpoint. It is used by other pages that may utilize this endpoint and want to show the device\'s name. It is for your reference only.'
			), 'Endpoint Name:');

		echo form::input('endpoint[name]');
	?>
	</div>


	<div class="field">
	<?php
		echo form::label(array(
			'for'=>'endpoint[mac]',
			'hint'=>'Network identifier this endpoint',
			'help'=>'This is a code, made up of numbers and letters a-f, often written underneat the phone. It is 12 characters long, and may have colons between pairs of characters.'
			), 'Endpoint MAC address:');
		echo form::input('endpoint[mac]',NULL,'onkeyup="check_oui(this)" onchange="check_oui(this)"');
	?>
	</div>
		
	<div class="field">
	<?php
		echo form::label(array(
			'for'=>'endpoint[brand]',
			'hint'=>'Manufacturer of this phone',
			), 'Make:');
		echo $brandnameselect;
	?>
	</div>
		
	<div class="field">
	<?php
		echo form::label(array(
			'for'=>'endpoint[model]',
			'hint'=>'Model of this phone',
			), 'Model:');
		echo "<select id=model_select onchange='update_model();' name=endpoint[model] >$modelselect</select>\n";
	?>
	</div>
		
	<?php echo form::close_section(); ?>
	<?php if (!is_null($models)) { ?>

		<?php echo form::open_section('Lines'); ?>
		<style>
			#line_list .ui-tabs-panel { border:1px solid #CCCCCC !important; }
			#line_list .ui-widget-header { background:#FFFFFF !important; border:0 !important; }
			#button_list .ui-tabs-panel { border:1px solid #CCCCCC !important; }
			#button_list .ui-widget-header { background:#FFFFFF !important; border:0 !important; }
		</style>
	
	
	
		<div id="line_list" style="border:0 !important;">
			<ul>
			<?php print $tabs; ?>
			</ul>
			<?php print $linelist; ?>
		</div>
	
	
		<?php echo form::close_section(); ?>
		<?php if ($buttons>0) { ?>
			<?php echo form::open_section('Buttons'); ?>
				<div id="button_list">
					<ul>
						<?php print $buttontabs; ?>
					</ul>
					<?php print $buttonlist; ?>
					<?php echo form::close_section(); ?>
				</div>
		<?php } ?>
	<?php } ?>

	<?php echo form::close(TRUE); ?>
	
		<?php javascript::codeBlock(NULL, array('scriptname' => 'lineList')); ?>
			$('#line_list').tabs({ fxAutoHeight: true });
			$('#button_list').tabs({ fxAutoHeight: true });
		<?php javascript::blockEnd(); ?>
<script type="text/javascript">
			for (var lineno=<?php print $models["lines"]+1; ?>; lineno--;) {
				change_line(lineno);
			}
</script>

</div>
