<?php echo form::open_section('Shared Presence'); ?>
	<div class="field">
	<?php
		echo form::label(
			array(
				'for' => 'sharedpresence[mode]',
		    	'hint' => 'Shared Presence Mode',
		    	'help' => 'Set the first interfeace to First, then the remaining interfaces to Subsequent.'
		    ),
		    'Shared Presence Mode:'
		);
	    echo form::dropdown('sharedpresence[mode]', array('false' => 'Off', 'true' => 'First Interface', 'passive' => 'Subsequent Interfaces'));
    ?>
	</div>
	<div class="field">
	<?php
		echo form::label(
			array(
				'for' => 'sharedpresence[spd_id]',
		    	'hint' => 'Shared Presence Database',
		    	'help' => 'Select the database to share presence.'
		    ),
		    'Shared Presence Database:'
		);
	    echo form::dropdown('sharedpresence[spd_id]', sharedpresence_helper::getDBList());
    ?>
	</div>
	<div class="field">
	<?php
		echo form::label(
			array(
				'for' => 'sharedpresence[send_info]',
		    	'hint' => 'Send Presence Info?',
		    	'help' => 'Specify whether or not to send presence information when users register. Default is not to send presence information.'
		    ),
		    'Send Presense:'
		);
	    echo form::dropdown('sharedpresence[send_info]', array('false' => 'No', 'true' => 'Yes', 'first-only' => 'First Time Only'));
    ?>
	</div>
    <?php echo form::close_section(); ?>
