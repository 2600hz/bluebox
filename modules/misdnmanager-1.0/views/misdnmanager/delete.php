<?php
message::render();
?>

<div id="misdnmanager_delete_header"
  class="txt-center delete misdnmanager tab_header">
<h2><?php echo $title;?></h2>
</div>

<div id="misdnmanager_delete_form" class="form delete misdnmanager">
<?php
echo form::open();

echo form::open_fieldset();
echo form::legend('Card');

echo form::label('card[MisdnCardModel][MisdnCardVendor][vendor]', 'Vendor:');
echo form::input(array('name' => 'card[MisdnCardModel][MisdnCardVendor][vendor]', 'readonly' => true), $card->MisdnCardModel->MisdnCardVendor->vendor);

echo form::label('card[MisdnCardModel][model]', 'MisdnCardModel:');
echo form::input(array('name' => 'card[MisdnCardModel][model]', 'readonly' => true), $card->MisdnCardModel->model);

echo html::br();

echo form::label('card[pci_address]', 'PCI Address:');
echo form::input(array('name' => 'card[pci_addr]', 'readonly' => true), $card->pci_address);

echo form::label('card[MisdnCardModel][pci_subsys_id]', 'PCI Subsystem ID:');
echo form::input(array('name' => 'card[MisdnCardModel][pci_subsys_id]', 'readonly' => true), $card->MisdnCardModel->pci_subsys_id);

echo html::br();

echo form::label('card[description]', 'Description:');
echo form::textarea(array('name' => 'card[description]', 'readonly' => true), $card->description);

echo form::close_fieldset();


echo form::open_fieldset();
echo form::legend('Confirm Deletion');

i18n('Are you sure you want to delete the above card?')->sprintf()->e();

echo form::close_fieldset();


echo form::open_fieldset(array('class' => 'buttons'));

echo form::submit('no', 'No');
echo form::submit('confirm', 'Yes');

echo form::close_fieldset();

echo form::close();
?></div>
