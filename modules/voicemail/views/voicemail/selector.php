<?php echo form::open_section('Route to Voicemail Box...'); ?>

    <div class="field">
    <?php
        echo form::label('number[options][voicemail]', 'Voicemail Box:');
        echo form::hidden('number[class_type]', 'VoicemailNumber');
        echo form::dropdown('number[foreign_id]', $voicemailBoxes);
    ?>
    </div>

<?php echo form::close_section(); ?>


<?php javascript::codeBlock(); ?>
$(document).bind('destination_voicemail_submit', function() {
    $('form#destination_selector input[name="friendly_name"]').val('Voicemail ' + $('.destination_voicemail #number_foreign_id option:selected').val() + ' (' + $('.destination_voicemail #number_foreign_id option:selected').text() + ')');
});
<?php javascript::blockEnd(); ?>