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
		'for'=>'package[registry][defaults][global][linedisplay]',
		'hint'=>'What to display next to line buttons on the phone',
		), 'Line Display:');

		echo form::dropdown("package[registry][defaults][global][linedisplay]",array('name'=>'Device Name','extension'=>'SIP Username'),$defaults['global']['linedisplay']);
	?>
	</div>

	<?php echo $additional_global_questions; ?>

	<?php echo form::close_section(); ?>

	<?php echo $additionalquestions; ?>

	<?php echo form::close(TRUE); ?>
</div>
