<div id="backup_update_header" class="update conferenece module_header">
    <h2><?php echo $title; ?></h2>
</div>


<?php echo html::anchor('backup/export', 'Export'); ?>


<?php echo form::open("backup/import")?>
<?php echo form::open_section('Import'); ?>
<?php
if(isset($list_options)) {
	echo form::dropdown('file', $list_options);
}
?>
<br /><br />
<?php echo form::submit('import', 'Import'); ?>
<?php echo form::close_section(); ?>
<?php echo form::close(); ?>
