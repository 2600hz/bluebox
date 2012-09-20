<?php defined('SYSPATH') or die('No direct access allowed.'); ?>
<div id="faxvarlist">
	<p>There are a number of variables available for use in the email subject and body:</p>
	<br>
	<p>Fax related variables</p>
	<ul class="faxlist">
		<li>\\\${fax_bad_rows} - Number of rows that were not successfully transmitted</li>
		<li>\\\${fax_document_total_pages} - Total number of pages to be transmitted (if available)</li>
		<li>\\\${fax_document_transferred_pages} - Total number of pages transmitted</li>
		<li>\\\${fax_ecm_requested}- 1 if ECM was requested or 0 if not</li>
		<li>\\\${fax_ecm_used} - On if ECM was used, or Off it was not</li>
		<li>\\\${fax_filename} - File name of the received fax</li>
		<li>\\\${fax_image_resolution} - Resolution of fax</li>
		<li>\\\${fax_image_size} - Size of image</li>
		<li>\\\${fax_local_station_id} - ID of local fax</li>
		<li>\\\${fax_result_code} - 0 on error otherwise &gt;= 1</li>
		<li>\\\${fax_result_text} - fax error string, provide info where an error has happened</li>
		<li>\\\${fax_remote_station_id} - ID of remote fax</li>
		<li>\\\${fax_success} - 0 on error, 1 on success</li>
		<li>\\\${fax_transfer_rate} - speed expressed in bauds (bit per seconds) like 14.400, 9.600, etc.</li>
		<li>\\\${fax_v17_disabled} - 1 if T.30 mode was enabled, 0 if not</li>
		<li>\\\${t38_gateway_format} - "audio" or "udptl"</li>
		<li>\\\${t38_peer} - "self" or "peer"</li>
	</ul>
	<br>
	<p>All Freeswitch and related variables, including (but not limited to):</p>
	<ul class="faxlist">
		<li>${uuid} - ID of call.  The format of the tiff file is infax-${uuid}.tif</li>
		<li>${base_dir} - Freeswitch installation directory</li>
		<li>${caller_id_name} - caller id name set by the inbound cal</li>
		<li>${caller_id_number} - caller id number set by the inbound call</li>
	</ul>
	<p>additional available variables documented <a href="http://wiki.freeswitch.org/wiki/Channel_Variables" target="_blank">here</a></p>
</div>
<div class="field">
	<?php
		echo form::label(array(
				'for' => 'faxprofile[registry][system_email]',
				'hint' => 'Email to send failure/error messages to',
				'help' => 'If specified, abnormal failure and/or error messages will be sent to this email.'
			),
			'Admin Email:'
		);
		echo form::input('faxprofile[registry][system_email]');
	?>
</div>
<div class="field">
	<?php
		echo form::label(array(
				'for' => 'faxprofile[registry][from_address]',
				'hint' => 'Address email will appear to come from',
				'help' => 'This email will be used as the from address for the email that the body of fax is attached to. Default: fax@<i>localhostname</i>'
			),
			'From Address:'
		);
		echo form::input('faxprofile[registry][from_address]');
	?>
</div>
<div class="field">
	<?php
		echo form::label(array(
				'for' => 'faxprofile[registry][from_name]',
				'hint' => 'Name email will appear to come from',
				'help' => 'This name will be used as the from name for the email that the fax is attached to. Default: Fax System'
			),
			'From Name:'
		);
		echo form::input('faxprofile[registry][from_name]');
	?>
</div>
<div class="field">
	<?php
		echo form::label(array(
				'for' => 'faxprofile[registry][email_subject]',
				'hint' => 'Subject of email',
				'help' => 'Subject of email that fax will be attached to.  Default: Fax Received from <i>remoteident</i> (<i>cidname</i>/<i>cidnum</i>)'
			),
			'Email Subject:'
		);
		echo form::input('faxprofile[registry][email_subject]');
	?>
</div>
<div class="field">
	<?php
		echo form::label(array(
				'for' => 'faxprofile[registry][email_body]',
				'hint' => 'Body of email',
				'help' => 'Subject of email that fax will be attached to.  Defaults to: Fax Received from <i>remoteident</i> (<i>cidname</i>/<i>cidnum</i>) <i>totalpages</i> were sent, and <i>pagesreceived</i> were received.  The result code was <i>resultcode</i> and message was <i>resulttext</i>'
		),
			'Email Body:'
		);
		echo form::textarea(array('name' => 'faxprofile[registry][email_body]', 'rows' => 4, 'cols' => 50));
	?>
</div>
<div class="field">
	<?php
		echo form::label(array(
				'for' => 'faxprofile[registry][dest_email]',
				'hint' => 'Address to send fax to',
				'help' => 'The fax will be emailed to this address'
			),
			'To Address:'
		);
		echo form::input('faxprofile[registry][dest_email]');
	?>
</div>
<div class="field">
	<?php
		echo form::label(array(
				'for' => 'faxprofile[registry][send_status]',
				'hint' => 'Check to disable status emails to destination email',
				'help' => 'If checked, emails of errors or failures will not be sent to the destination email.'
			),
			'Disable Status Email:'
		);
		echo form::checkbox('faxprofile[registry][send_status]');
	?>
</div>
