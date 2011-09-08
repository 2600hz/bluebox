<link rel="stylesheet" type="text/css" href="/assets/css/jquery/smoothness/ui.all.css" media="screen" />
<script type="text/javascript" src="/assets/js/jquery/ui.accordion.js"></script>
<script type="text/javascript" src="/assets/js/jquery/jquery.dependent.js"></script>
<script type="text/javascript" src="/assets/js/jquery/ui.tabs.js"></script>
<script type="text/javascript" src="/assets/js/mustache.js"></script>

<div id="endpoint_update_header" class="txt-center update endpoint module_header">
	<h2><?php echo $title; ?></h2>
</div>

<div id="endpoint_update_form" class="update endpoint">

	<?php echo form::open(); ?>
	<?php echo form::open_section('Endpoint information'); ?>

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
		echo form::input('endpoint[mac]');
	?>
	</div>
		
	<div class="field">
	<?php
		echo form::label(array(
			'for'=>'brandandmodel',
			'hint'=>'Manufacturer and model of this phone',
			), 'Make And Model:');
		echo $brandandmodelselect;
	?>
	</div>
		
	<?php echo form::close_section(); ?>

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

	<?php echo form::close(TRUE); ?>

</div>
