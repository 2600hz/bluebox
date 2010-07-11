<?php echo form::open_section('Route to an External Destination...'); ?>
    <?php
        echo form::hidden('number[class_type]', 'ExternalXfer');
        echo form::hidden('number[foreign_id]', 0);
    ?>

    <div class="field">
    <?php
        echo form::label('number[options][timeout]', 'Ring this number for:');
        echo form::input('number[options][timeout]');
        echo ' seconds';
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('number[options][timeout]', 'If no answer, transfer to: ');
        echo numbering::numbersDropdown(array('name' => 'numbers[failback]', 'contextAware' => TRUE, 'optGroups' => FALSE));
    ?>
    </div>

<?php echo form::close_section(); ?>
