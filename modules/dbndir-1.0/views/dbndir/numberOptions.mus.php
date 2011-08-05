<?php echo form::open_section('Options'); ?>
	<div class="field">
		<?php
			echo form::label(array(
					'for' => 'dbndir[tran_context_id]',
					'hint' => 'Context to use when transfering',
					'help' => 'Context to transfer the calls to.'
				),
				'Transfer Context:'
			);
			echo numbering::selectContext('dbndir[tran_context_id]');
		?>
	</div>
<?php echo form::close_section(); ?>