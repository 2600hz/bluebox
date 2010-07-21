<?php javascript::codeBlock(); ?>
$(document).bind('destination_featurecode_submit', function() {
    $('form#destination_selector input[name="friendly_name"]').val('Feature Code ' + $('.destination_featurecode #number_foreign_id option:selected').val() + ' (' + $('.destination_featurecode #number_foreign_id option:selected').text() + ')');
});

<?php javascript::blockEnd(); ?>
