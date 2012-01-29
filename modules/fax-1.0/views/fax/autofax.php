<?php 
echo form::open_section('Fax Autodetect');
?>
	<div class="field">
		<?php
			echo form::label(array(
					'for' => 'fax[autodetect]',
					'hint' => 'Enable fax autodetect on this number.',
					'help' => 'If enabled, the system will attempt to autodetect inbound faxes and transfer to the chosen extension if the inbound fax tone is detected.'
				),
				'Enable Autodetection:'
			);
			echo form::checkbox('fax[autodetect]');
		?>
	</div>
	<div class="field">
		<?php
			echo form::label(array(
					'for' => 'fax[autodetect_number]',
					'hint' => 'Transfer to this number on fax autodetect.',
					'help' => 'The system will attempt to transfer the call to this extension if fax autodetect is enabled and the inbound fax tone is detected.'
				),
				'Autodetection Transfer Number:'
			);
			echo numbering::numbersDropdown(array('name' => 'fax[autodetect_number]', 'classType' => 'FaxProfileNumber', 'optGroups' => FALSE));
		?>
	</div>
<?php 
echo form::close_section(); 
?>