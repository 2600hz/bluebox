<?php defined('SYSPATH') or die('No direct access allowed.');?>

<div id="callmanager_header" class="modules callmanager module_header">
	<h2><?php echo __('Record Call')?></h2>
</div>
<?php
echo form::open();
echo form::hidden('uuid', $uuid);
if ($record_file)
{
	echo 'This call is currently being recorded to <br><span style="font-weight: bold;">' . $record_file . '</span><br>';
	echo 'Would you like to stop it?';
	echo form::close('yes_no');
}
else
{
?>
	<div class="field">
	<?php
		echo form::label(array(
				'for' => 'file_tag',
				'hint' => 'Recording file tag.',
				'help' => 'Name of file to save audio to.  The system recording path will be prepended and the extention .wav added automaticly.<br>You can use the following variables:<ul><li>%date% - date in yyyy-mm-dd_hh:mm:ss format</li><li>%uuid% - Unique ID of call</li></ul>'
			),
			'File Tag:'
		);
		echo form::input('file_name', $file_name);
	?>
	</div>
	<div class="field">
	<?php
		echo form::label(array(
				'for' => 'max_record_time',
				'hint' => 'Maximum recording length.',
				'help' => 'Maximum number of seconds to record.  Keeps the file size from getting out of hand. 0 is disabled'
			),
			'Max Record Secs:'
		);
		echo form::input('max_record_time', 0);
	?>
	</div>
	<?php echo form::close('ok_cancel');
}
?>