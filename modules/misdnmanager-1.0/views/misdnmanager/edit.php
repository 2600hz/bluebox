<div id="misdnmanager_add_header"
  class="txt-center add misdnmanager tab_header">
<h2><?php echo $title;?></h2>
</div>

<?php
message::render();
?>

<div id="misdnmanager_add_form" class="txt-left form add misdnmanager">

<?php 
echo form::open();

echo form::open_fieldset();
echo form::legend('Info');

echo form::label('misdncard[MisdnCardModel][MisdnCardVendor][vendor]', 'Vendor:');
//echo form::input(array('name' => 'misdncard[MisdnCardModel][MisdnCardVendor][vendor]', 'readonly' => true));
echo form::input(array('name' => 'misdncard[MisdnCardModel][MisdnCardVendor][vendor]', 'readonly' => true), $misdncard->MisdnCardModel->MisdnCardVendor->vendor);

echo form::label('misdncard[MisdnCardModel][model]', 'Model:');
//echo form::input(array('name' => 'misdncard[MisdnCardModel][model]', 'readonly' => true));
echo form::input(array('name' => 'misdncard[MisdnCardModel][model]', 'readonly' => true), $misdncard->MisdnCardModel->model);

echo html::br();

echo form::label('misdncard[pci_address]', 'PCI Address:');
//echo form::input(array('name' => 'misdncard[pci_address]', 'readonly' => true));
echo form::input(array('name' => 'misdncard[pci_addr]', 'readonly' => true), $misdncard->pci_address);

echo form::label('misdncard[MisdnCardModel][pci_subsys_id]', 'PCI Subsystem ID:');
//echo form::input(array('name' => 'misdncard[MisdnCardModel][pci_subsys_id]', 'readonly' => true));
echo form::input(array('name' => 'misdncard[MisdnCardModel][pci_subsys_id]', 'readonly' => true), $misdncard->MisdnCardModel->pci_subsys_id);

echo html::br();

echo form::label('misdncard[description]', 'Description (optional):');
//echo form::textarea('misdncard[description]');
echo form::textarea('misdncard[description]', $misdncard->description);

echo form::close_fieldset();

echo form::open_fieldset();
echo form::legend('Settings');

echo form::label('settings[ulaw]', 'Use &mu;-Law Codec:');
echo form::dropdown('settings[ulaw]',
array('' => 'Not Set', 'yes' => 'Yes', 'no' => 'No'), isset($settings['ulaw']) ? $settings['ulaw'] : '');

echo form::label('settings[dtmf]', 'Enable DTMF Detection:');
echo form::dropdown('settings[dtmf]',
array('' => 'Not Set', 'yes' => 'Yes', 'no' => 'No'),
isset($settings['dtmf']) ? $settings['dtmf'] : '');

echo form::label('settings[pcm_slave]', 'PCM Slave Mode:');
echo form::dropdown('settings[pcm_slave]',
array('' => 'Not Set', 'yes' => 'Yes', 'no' => 'No'),
isset($settings['pcm_slave']) ? $settings['pcm_slave'] : '');

echo form::label('settings[ignore_pcm_frameclock]', 'Ignore PCM Frameclock');
echo form::dropdown('settings[ignore_pcm_frameclock]',
array('' => 'Not Set', 'yes' => 'Yes', 'no' => 'No'),
isset($settings['ignore_pcm_frameclock']) ? $settings['ignore_pcm_frameclock'] : '');


echo form::label('settings[rxclock]', 'RX Clock:');
echo form::dropdown('settings[rxclock]',
array('' => 'Not Set', 'yes' => 'Yes', 'no' => 'No'),
isset($settings['rxclock']) ? $settings['rxclock'] : '');



echo form::label('settings[crystalclock]', 'Crystal Clock:');
echo form::dropdown('settings[crystalclock]',
array('' => 'Not Set', 'yes' => 'Yes', 'no' => 'No'),
isset($settings['crystalclock']) ? $settings['crystalclock'] : '');


echo form::label('settings[watchdog]', 'Watchdog:');
echo form::dropdown('settings[watchdog]',
array('' => 'Not Set', 'yes' => 'Yes', 'no' => 'No'),
isset($settings['watchdog']) ? $settings['watchdog'] : '');

echo form::close_fieldset();

$masterclock = isset($settings['masterclock_port']) ? $settings['masterclock_port'] : 1;

echo form::open_fieldset();
echo form::legend('Ports');

echo $grid;

echo form::close_fieldset();

echo form::open_fieldset(array('class' => 'buttons'));

echo form::submit('submit', 'Save');

echo form::close_fieldset();

echo form::close();

?>
</div>