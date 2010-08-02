<div id="misdnmanager_ported_header"
	class="txt-center ported misdnmanager tab_header">
<h2><?php echo $title;?></h2>
</div>

<?php
message::render();
?>

<div id="misdnmanager_ported_form"
	class="txt-left form ported misdnmanager"><?php
	echo form::open();

	echo form::open_fieldset();
	echo form::legend('Port ' . $misdnport->number . ' Settings');
	
	echo form::label('misdnport[description]', 'Description:');
	echo form::input('misdnport[description]', $misdnport['description']);
	
	echo html::br();

	echo form::label('misdnport[mode]', 'Mode:');
	echo form::dropdown('misdnport[mode]',
	//    array('' => 'Disabled', 'te' => 'TE - Terminal Equipment', 'nt' => 'NT - Network Terminal'),
	//    array('te' => 'TE - Terminal Equipment', 'nt' => 'NT - Network Terminal'));
	array('te' => 'TE - Terminal Equipment', 'nt' => 'NT - Network Terminal'),
	isset($misdnport['mode']) ? $misdnport['mode'] : 'te');

	echo form::label('misdnport[link]', 'Link Type:');
	echo form::dropdown('misdnport[link]',
	array('ptp' => 'PTP - Point to Point', 'ptmp' => 'PTMP - Point to Multi-Point'),
	isset($misdnport['link']) ? $misdnport['link'] : 'ptp');

	echo form::label('misdnport[trunk]', 'Use as trunk:');
	echo form::checkbox('misdnport[trunk]', 'yes',
	isset($misdnport['trunk']) ? $misdnport['trunk'] : false);

	echo form::close_fieldset();

	echo form::open_fieldset(array('class' => 'buttons'));

	echo form::submit('submit', 'Save');

	echo form::close_fieldset();

	echo form::close();
