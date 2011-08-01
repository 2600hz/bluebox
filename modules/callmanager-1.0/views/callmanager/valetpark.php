<?php defined('SYSPATH') or die('No direct access allowed.');?>

<div id="callmanager_header" class="modules callmanager module_header">
    <h2><?php echo __('Park Call')?></h2>
</div>
<?php
echo form::open();
echo form::hidden('uuid', $uuid);
?>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'destlot',
                    'hint' => 'Lot to park call in',
                    'help' => 'Please select the lot that you would like to park this call in from the list'
                ),
                 'Park call in'
            );
        echo numbering::destinationsDropdown(array('name' => 'destlot',  'nullOption' => '', 'assigned' => true, 'classType' => 'ValetParkingLotNumber'));
        ?>
        </div>
<?php
echo form::close('ok_cancel');
?>
