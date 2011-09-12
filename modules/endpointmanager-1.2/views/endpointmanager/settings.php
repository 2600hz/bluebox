<div id="endpoint_settings_header" class="update endpoint_settings module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="endpoint_global_form" class="update endpoint">
	<?php echo form::open(); ?>

	<?php echo form::open_section('Global Settings'); ?>
	<div class='field'>
	<?php echo form::label(array(
		'for'=>'package[registry][defaults][global][timezone]',
		'hint'=>'Default timezone for all phones',
		'help'=>'Default timezone for all phones',
		), 'Default Timezone:');

		echo timezone::dropdown("package[registry][defaults][global][timezone]",$savedtimezone);
	?>
	</div>

	<div class='field'>
	<?php echo form::label(array(
		'for'=>'package[registry][defaults][global][vlan]',
		'hint'=>'VLAN to put the phone in (0-4094)',
		'help'=>'Specify which VLAN the phone should be in.<br>Valid values are 0 through 4094.<br>0 means do not use a VLAN.<br>If in doubt, use 0.',
		), 'VLAN Number');

		echo form::input('package[registry][defaults][global][vlan]');
	?>
	</div>

	<div class='field'>
	<?php echo form::label(array(
		'for'=>'package[registry][defaults][global][pcp]',
		'hint'=>'Priority Code Point/QOS',
		'help'=>'Specify the priority (QOS) for the VOIP traffic.<br>Valid values are 0 through 7.<br>Higher number means higher priority.'
		), 'Priority Code Point/QOS');

		echo form::input('package[registry][defaults][global][pcp]');
	?>
	</div>

	<?php echo form::close_section(); ?>

	<?php echo $additionalquestions; ?>

	<?php echo form::close(TRUE); ?>
</div>
