<?php defined('SYSPATH') or die('No direct access allowed.');?>

<div id="callmanager_header" class="modules callmanager module_header">
    <h2><?php echo __('Place Call On Hold')?></h2>
</div>
<?php
echo form::open();
echo form::hidden('uuid', $uuid);
if ($onHold)
{
	echo 'This call is currently on hold.  Do you want to take it off?';
	echo form::hidden('holdaction','off');
}
else
{	echo 'Really place this call on hold???';
	echo form::hidden('holdaction','');
}
echo form::close('yes_no');
?>
