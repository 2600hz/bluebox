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
		document.getElementById('endpointmanager_edit').submit();
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
	<div id='model_settings'>
	<?php if (!is_null($models)) { ?>

	<?php echo form::open_section('Lines'); ?>
	<style>
		#line_list .ui-tabs-panel { border:1px solid #CCCCCC !important; }
		#line_list .ui-widget-header { background:#FFFFFF !important; border:0 !important; }
	</style>
	<?php javascript::codeBlock(NULL, array('scriptname' => 'lineList')); ?>
		$('#line_list').tabs({ fxAutoHeight: true });
	<?php javascript::blockEnd(); ?>



	<div id="line_list" style="border:0 !important;">
		<ul>
			<?php 
				for ($line=1; $line<=$models['lines']; $line++) {
					print " <li><a href='#line_$line'><span style='font-size: 90%'>Line $line</span></a></li>\n";
				}
			?>
		</ul>
		<?php 
			for ($line=1; $line<=$models['lines']; $line++) {
				print "<div id='line_$line' class='assign_number_tab'>";
				print '<div class="field">';
				echo form::label(array(
					'for'=>"lines[$line]",
					'hint'=>'Device for this line',
					), "Device for this line:");
				print "<select name='lines[$line][sip]'><option value=''>Unused</option>$deviceSelect[$line]</select>";
				print '</div></div>';
			}
		?>
	</div>


	<?php echo form::close_section(); ?>
	<?php } ?>
	</div>

	<?php echo form::close(TRUE); ?>

</div>
