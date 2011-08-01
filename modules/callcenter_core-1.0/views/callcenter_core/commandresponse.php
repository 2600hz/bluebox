<?php defined('SYSPATH') or die('No direct access allowed.');?>

<div id="callmanager_header" class="modules callmanager module_header">
    <h2><?php echo __('Result')?></h2>
</div>
<?php echo $commandresponse;
echo form::open();
echo form::close('ok_only');
?>