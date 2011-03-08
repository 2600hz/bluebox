<?php echo form::open_section('Areacode'); ?>

	<div class="field">
		<?php echo form::label(array('for' => 'device[registry][areacode]',
					     'help' => 'To use this value in routing, just add ${areacode} to the prepend field on a trunk.'),
					     'Areacode:');
		      echo form::input('device[registry][areacode]');
		?>
	</div>

<?php echo form::close_section(); ?>
