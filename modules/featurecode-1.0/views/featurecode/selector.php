<?php echo form::open_section('Route to an Feature Code...'); ?>

    <div class="field">
    <?php
        echo form::label('number[options][featurecode]', 'Feature Code:');
        echo form::hidden('number[class_type]', 'FeatureCodeNumber');
        echo form::dropdown('number[foreign_id]', $featureCodes);
    ?>
    </div>

<?php echo form::close_section(); ?>


<?php javascript::codeBlock(); ?>
$(document).bind('destination_featurecode_submit', function() {
    $('form#destination_selector input[name="friendly_name"]').val('Feature Code ' + $('.destination_featurecode #number_foreign_id option:selected').val() + ' (' + $('.destination_featurecode #number_foreign_id option:selected').text() + ')');
});

<?php javascript::blockEnd(); ?>
