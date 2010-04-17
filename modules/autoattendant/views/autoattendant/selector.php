<?php echo form::open_section('Route to an Auto Attendant...'); ?>

    <div class="field">
    <?php
        echo form::label('number[options][autoattendant]', 'Auto Attendant:');
        echo form::hidden('number[class_type]', 'AutoAttendantNumber');
        echo form::dropdown('number[foreign_id]]', $autoAttendants);
    ?>
    </div>

<?php echo form::close_section(); ?>


<?php javascript::codeBlock(); ?>
$(document).bind('destination_autoattendant_submit', function() {
    $('form#destination_selector input[name="friendly_name"]').val('Auto Attendant ' + $('.destination_autoattendant #number_foreign_id option:selected').val() + ' (' + $('.destination_autoattendant #number_foreign_id option:selected').text() + ')');
});

<?php javascript::blockEnd(); ?>
