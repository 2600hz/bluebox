<?php echo form::open_section('Route to a Device...'); ?>

    <div class="field">
    <?php
        echo form::label('number[options][device]', 'Device to Ring:');
        echo form::hidden('number[class_type]', 'DeviceNumber');
        echo form::dropdown('number[foreign_id]', $devices);
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('number[options][ringtype]', 'Ring Type: ');
        echo form::dropdown('number[options][ringtype]', array('Ringing' => 'Ringing', 'MOH' => 'Hold Music'));
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('number[options][timeout]', 'Ring this device for:');
        echo form::input('number[options][timeout]');
        echo ' seconds';
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('number[options][fallback]', 'If no answer, transfer to: ');
        //echo numbering::selectContext('number[options][timeout_context]', $fallback_context);
        echo numbering::numbersDropdown('number[options][fallback]');
    ?>
        <small><B>Note: leave blank to default to voicemail, if configured</B></small>
    </div>

<?php echo form::close_section(); ?>


<?php javascript::codeBlock(); ?>
$(document).bind('destination_devices_submit', function() {
    $('form#destination_selector input[name="friendly_name"]').val('Device ' + $('.destination_devices #number_foreign_id option:selected').val() + ' (' + $('.destination_devices #number_foreign_id option:selected').text() + ')');
});
<?php javascript::blockEnd(); ?>
