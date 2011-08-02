<?php defined('SYSPATH') or die('No direct access allowed.');?>

<div id="callmanager_header" class="modules callmanager module_header">
    <h2><?php echo __('Hang Up Call')?></h2>
</div>
<?php
echo form::open();
echo form::hidden('uuid', $uuid);
echo 'Do you really want to hang up this call???';
echo form::close('yes_no');
?>
