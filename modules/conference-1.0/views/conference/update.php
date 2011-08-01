<div id="conferences_update_header" class="update conferenece module_header">

    <h2><?php echo $title; ?></h2>

</div>

<div id="conferences_update_form" class="update conferenece">

	<?php echo form::open(); ?>

	<?php echo form::open_section('Conference Information'); ?>

	<div class="field">
		<?php
		echo form::label('conference[name]', 'Conference Name:');
		echo form::input('conference[name]');
		?>
	</div>

	<div class="field">
		<?php
		echo form::label(array(
			'for' => 'conference[pins][0]',
			'hint' => 'Leave blank for no pin'
				), 'Pin:'
		);
		echo form::input('conference[pins][0]');
		?>
	</div>

	<?php echo form::close_section(); ?>

	<?php echo form::open_section('Conference Features'); ?>

	<div class="field">
		<?php
		echo form::label('conference[registry][moh_type]', 'Pre-Conference Music');
		echo form::dropdown('conference[registry][moh_type]', array('local_stream://moh' => 'Music On Hold', 'silence' => 'Silence'));
		?>
	</div>

	<div class="field">
		<?php
		echo form::label('conference[registry][energy-level]', 'Minimum Energy Level');
		echo form::input('conference[registry][energy-level]');
		?>
	</div>

	<div class="field">
		<?php
		echo form::label(array(
			'for' => 'conference[registry][size-limit]',
			'hint' => 'Leave blank or zero for unlimited'
				), 'Max Participants');
		echo form::input('conference[registry][size-limit]');
		?>
	</div>

	<div class="field">
		<?php
		echo form::label('conference[registry][record]', 'Record conference?');
		echo form::checkbox('conference[registry][record]');
		?>
	</div>

	<div class="field">
		<?php
		echo form::label('conference[registry][comfort-noise]', 'Generate Comfort Noise?');
		echo form::checkbox('conference[registry][comfort-noise]');
		?>
	</div>


	<div class="field">
		<?php
		echo form::label(array(
			'for' => 'conference[registry][media-before]',
			'hint' => 'Play a media file before the conference'
				), 'Enable Media before conference');
		echo form::checkbox('conference[registry][media-before]');
		?>
	</div>

	<?php echo form::close_section(); ?>

	<?php if(FALSE): ?>

		<?php echo form::open_section('Music and Audio'); ?>

		<div class="field">
			<?php
			echo form::label('conference[registry][record_location]', 'Recorded File Location:');
			echo form::input('conference[registry][record_location]');
			?>
		</div>

		<div class="field">
			<?php
			echo form::label('conference[registry][conference_soundmap_id]', 'Event Sound Map');
			echo form::dropdown('conference[registry][conference_soundmap_id]', array(
				1 => 'Default'
			));
			?>
		</div>

		<div class="field">
			<?php
			echo form::label('conference[registry][comfort_noise]', 'Generate Comfort Noise?');
			echo form::checkbox('conference[registry][comfort_noise]');
			?>
		</div>

		<?php echo form::close_section(); ?>

	<?php endif; ?>

	<?php
	if(isset($views)) {
		echo subview::renderAsSections($views);
	}
	?>

	<?php echo form::close(TRUE); ?>

</div>


<script type="text/javascript">
	if(!$('#conference_registry_media_before').is(':checked')){
		$('#legend_media').hide();
		$('#media_tabs').hide();
	}
	
	$('#conference_registry_media_before').click(function(){	
		$('#legend_media').toggle();
		$('#media_tabs').toggle();
	});
</script>