<?php echo form::open_section('Route to Ring Group...'); ?>

    <div class="field">
    <?php
        echo form::label('number[options][ringtype]', 'Ring Type: ');
        echo form::dropdown('number[options][ringtype]', array('Ringing', 'Hold Music', 'Screaming Monkeys'));
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('number[options][timeout]', 'Ring this group for:');
        echo form::input('number[options][timeout]');
        echo ' seconds';
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('number[options][ringgroup]', 'Ring Group:');
        echo form::hidden('number[class_type]', 'RingGroupNumber');
        echo form::dropdown('number[foreign_id]', $ringGroups);
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('number[options][timeout]', 'If no answer, transfer to: ');
        echo numbering::selectContext('number[options][timeout_context]', $fallback_context);
        echo form::dropdown('number[options][timeout]', $fallback_number);
    ?>
    </div>

<?php echo form::close_section(); ?>


<?php javascript::codeBlock(); ?>
$(document).bind('destination_ringgroup_submit', function() {
    $('form#destination_selector input[name="friendly_name"]').val('Ring Group ' + $('.destination_ringgroup #number_foreign_id option:selected').val() + ' (' + $('.destination_ringgroup #number_foreign_id option:selected').text() + ')');
});
<?php javascript::blockEnd(); ?>