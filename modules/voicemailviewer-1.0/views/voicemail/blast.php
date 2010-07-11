<?php
    jquery::addPlugin('multiselect');
    jquery::addQuery('.multiselect')->multiselect();
?>


<?php message::render(); ?>
<div class="form">
<?
echo form::open();
?>
<fieldset>
<legend>
    Voicemail Blasting
</legend>
 
  <div class="clear"></div>
  <div style="width: 100%;">
   <?php echo form::dropdown(array('name' => 'blast[]', 'class' => 'multiselect',  'multiple' => 'multiple', 'size' => 15, 'id' => 'blast'), $endpoints);?>
  </div>  
<div class="clear"></div>


<?php echo FileManager::dropdown('file_id', '', array('audio'));?>
<?php echo form::submit('confirm', 'Blast');?>
</fieldset>
<?php echo form::close('');?>
</div>

