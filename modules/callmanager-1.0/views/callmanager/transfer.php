<?php defined('SYSPATH') or die('No direct access allowed.');?>

<div id="callmanager_header" class="modules callmanager module_header">
    <h2><?php echo __('Transfer Call')?></h2>
</div>
<?php
echo form::open();
echo form::hidden('uuid', $uuid);
?>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'destext',
                    'hint' => 'Number to transfer the call to.',
                    'help' => 'Please select the number that you would like to transfer this call to from the list'
                ),
                 'Transfer call to'
            );
        echo numbering::destinationsDropdown(array('name' => 'destext',  'nullOption' => '', 'assigned' => true, 'classType' => $classType), $userext);
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'campon',
                    'hint' => 'Do you want to camp if the call is rejected?',
                    'help' => 'Camping is a function by which the call is placed on hold and transfered once the extension is availale.'
                ),
                 'Camp On:'
            );
        echo form::checkbox(array('name' => 'campon'), 'yes');
        ?>
        </div>
<?php
echo form::close('ok_cancel');
?>
