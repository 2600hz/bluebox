<div id="endpoint_settings_header" class="update endpoint_settings module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="endpoint_global_form" class="update endpoint">
	<?php echo form::open(); ?>

	<?php echo form::open_section('Global Settings'); ?>
	<?php echo form::label(array(
		'for'=>'package[registry][defaults][global][timezone]',
		'hint'=>'Default timezone for all phones',
		'help'=>'Default timezone for all phones',
		), 'Default Timezone:');

		echo timezone::dropdown("package[registry][defaults][global][timezone]",$savedtimezone);
	?>

	<?php echo form::close_section(); ?>

	<?php echo form::close(TRUE); ?>
</div>
