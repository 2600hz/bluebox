<?php echo form::open_section('Encryption'); ?>
	<div class="field">
	<?php
		echo form::label(
			array(
				'for' => 'sipencryption[type]',
		    	'hint' => 'Encryption Type',
		    	'help' => 'Use either TLSv1 or SSLv23.'
		    ),
		    'Encryption Type:'
		);
	    echo form::dropdown('sipencryption[type]', array('tlsv1'=>'TLSv1', 'sslv23'=>'SSLv23'));
    ?>
	</div>
    <div class="field">
	<?php
		echo form::label(
			array(
				'for' => 'sipencryption[port]',
		    	'hint' => 'Port to listen on. ex. 5061',
		    	'help' => 'Port for daemon to listen on for encrypted connections. Normally one port above the non-encrypted port.'
		    ),
		    'SSL/TLS Port:'
		);
        echo form::input('sipencryption[port]');
    ?>
    </div>
    <div class="field">
	<?php
		echo form::label(
			array(
				'for' => 'sipencryption[certdir]',
		    	'hint' => 'Certificate Storage Directory',
		    	'help' => 'Directory that the certificates are stored in. It must contain the CA certificate in \'CA/cacert.pem\' and the server certificate in \'agent.pem\''
		    ),
		    'SSL/TLS Certificate Directory:'
		);
        echo form::input('sipencryption[certdir]', '$${base_dir}/conf/ssl');
    ?>
    </div>
    <?php echo form::close_section(); ?>
