<?php defined('SYSPATH') or die('No direct access allowed.');?>

<div id="callmanager_header" class="modules callmanager module_header">
    <h2><?php echo __('Monitor Call')?></h2>
</div>
<?php
echo form::open();
echo form::hidden('uuid', $uuid);
echo 'Monitor call using ';
echo numbering::destinationsDropdown(array('name' => 'destext', 'classType' => 'DeviceNumber', 'optGroups' => false, 'nullOption' => '', 'assigned' => true), $userext);
echo form::close('ok_cancel');
?>
