<?php
/*
 * FreePBX Modular Telephony Software Library / Application
 *
 * Module:
 *
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 *
 * The Initial Developer of the Original Code is Michael Phillips <michael.j.phillips@gmail.com>.
 *
 * Portions created by the Initial Developer are Copyright (C)
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 * Michael Phillips
 *
 *
 */
?>

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
   <?php echo form::dropdown(array('name' => 'blast[]', 'class' => 'multiselect',  'multiple' => 'multiple', 'size' => 15, 'id' => 'blast'), $mailboxes);?>
  </div>
<div class="clear"></div>


<?php echo '<div>' .  form::dropdown('file_id', Media::files())  . '</div>'; ?>

<?php echo'<div>' .  form::submit('confirm', 'Blast') . '</div>';?>
</fieldset>
<?php echo form::close('');?>
<a href="<?= url::site('voicemailviewer/index') ?>">Cancel</a>
</div>

