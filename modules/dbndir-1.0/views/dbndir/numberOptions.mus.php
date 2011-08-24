<?php 
echo form::open_section('Options');
?>
	<div class="field">
		<?php
			echo form::label(array(
					'for' => 'number{{number_id}}[dialplan][dbndir][trancontextid]',
					'hint' => 'Context to use when transfering',
					'help' => 'Context to transfer the calls to.'
				),
				'Transfer Context:'
			);
			echo numbering::selectContext('number{{number_id}}[dialplan][dbndir][trancontextid]');
		?>
	</div>
<?php 
echo form::close_section(); 
?>