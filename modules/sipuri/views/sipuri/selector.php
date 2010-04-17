<?php echo form::open_section('Route to a SIP URI...'); ?>

    <div class="field">
    <?php
        echo form::label('number[options][timeout]', 'Ring this device for:');
        echo form::input('number[options][timeout]');
        echo ' seconds';
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('number[options][sipuri]', 'SIP URI:');
        echo form::input('number[options][sipuri]');
    ?>
    </div>

    <div class="field">
    <?php
        echo form::label('number[options][timeout]', 'If no answer, transfer to: ');
        echo form::dropdown('number[options][timeout]', $devices);
        echo numbering::selectContext('number[options][timeout_context]', $failback_context);
        echo ' seconds';
    ?>
    </div>

<?php echo form::close_section(); ?>


<?php javascript::codeBlock(); ?>
$('form #destination_selector #number_options_autoattendant').change(function() {
    $('form #destination_selector input[name="friendly_name"]').val('Booga booga');
});
<?php javascript::blockEnd(); ?>
