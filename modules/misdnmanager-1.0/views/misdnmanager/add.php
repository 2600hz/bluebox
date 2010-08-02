<div id="misdnmanager_add_header"
	class="txt-center add nisdnmanager tab_header">
<h2><?php echo $title;?></h2>
</div>

<?php
message::render();
?>

<div id="misdnmanager_add_form" class="txt-left form add misdnmanager">

<?php
$actionuri = 'misdnmanager/add/' . urlencode($card->MisdnCardModel->pci_subsys_id) . '/' . urlencode($card->pci_address);
echo form::open($actionuri);

echo form::open_fieldset();
echo form::legend('Info');

echo form::label('misdncard[MisdnCardModel][MisdnCardVendor][vendor]', 'Vendor:');
echo form::input(array('name' => 'misdncard[MisdnCardModel][MisdnCardVendor][vendor]', 'readonly' => true), $card->MisdnCardModel->MisdnCardVendor->vendor);

echo form::label('misdncard[MisdnCardModel][model]', 'Model:');
echo form::input(array('name' => 'misdncard[MisdnCardModel][model]', 'readonly' => true), $card->MisdnCardModel->model);

echo html::br();

echo form::label('misdncard[pci_address]', 'PCI Address:');
echo form::input(array('name' => 'misdncard[pci_addr]', 'readonly' => true), $card->pci_address);

echo form::label('misdncard[MisdnCardModel][pci_subsys_id]', 'PCI Subsystem ID:');
echo form::input(array('name' => 'misdncard[MisdnCardModel][pci_subsys_id]', 'readonly' => true), $card->MisdnCardModel->pci_subsys_id);

echo html::br();

echo form::label('misdncard[description]', 'Description (optional):');
echo form::textarea('misdncard[description]', $card->description);

echo form::close_fieldset();

echo form::open_fieldset(array('class' => 'buttons'));

echo form::submit('submit', 'Save');

echo form::close_fieldset();

echo form::close();

?></div>
