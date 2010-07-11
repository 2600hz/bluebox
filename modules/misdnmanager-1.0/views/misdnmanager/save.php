<div id="misdnmanager_add_header"
  class="txt-center add misdnmanager tab_header">
<h2><?php echo $title;?></h2>
</div>

<?php
message::render();
?>

<div id="misdnmanager_save_form" class="txt-left form save misdnmanager">

<?php

echo form::open();

echo form::open_fieldset();
echo form::legend('Confirm Save');

i18n('Are you sure you want to save the settings to disk?')->sprintf()->e();

echo form::close_fieldset();


echo form::open_fieldset(array('class' => 'buttons'));

echo form::submit('no', 'No');
echo form::submit('confirm', 'Yes');

echo form::close_fieldset();

echo form::close();
?></div>