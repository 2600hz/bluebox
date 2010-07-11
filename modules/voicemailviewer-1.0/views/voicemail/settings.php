<div class="form">
<?php echo form::open();?>
<fieldset>

<legend><?php echo Kohana::lang('voicemail.settings.header');?></legend>
<?php
echo form::label(array('name' => 'password', 'class' => 'text'), Kohana::lang('voicemail.password'));
echo form::input(array('name' => 'password', 'class' => 'text'), '');
echo form::submit('voicemail_settings', Kohana::lang('voicemail.save'));
echo form::close('');
?>
</fieldset>
</form>
