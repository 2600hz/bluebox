<?php defined('SYSPATH') or die('No direct access allowed.');
    jquery::addPlugin('multiselect');
    jquery::addQuery('.multiselect')->multiselect();

    message::render(); ?>

<div class="form">
<?php echo form::open(); ?>
<fieldset>
<legend>
    Voicemail Blasting
</legend>
 
  <div class="clear"></div>
  <div style="width: 100%;">
   <?php echo form::dropdown(array('name' => 'blast[]', 'class' => 'multiselect',  'multiple' => 'multiple', 'size' => 15, 'id' => 'blast'), $endpoints); ?>
  </div>  
<div class="clear"></div>


<?php echo FileManager::dropdown('file_id', '', array('audio'));
      echo form::submit('confirm', 'Blast'); ?>
</fieldset>
<?php echo form::close(''); ?>
</div>

