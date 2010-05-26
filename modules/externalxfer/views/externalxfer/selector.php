<?php echo form::open_section('Route to an External Destination...'); ?>

    <div class="field">
    <?php
        echo form::label('number[options][routetype]', 'Routing Method:');
        echo form::dropdown('number[options][routetype]', array(1 => 'via Trunk', 2 => 'via SIP URI'));
    ?>
    </div>

    <div id="via_trunk">
        <div class="field">
        <?php
            echo form::label(array('for' => 'number[options][number]'), 'Number to Call:');
            echo form::input('number[options][trunk_number]');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array('for' => 'number[options][trunk]', 'hint' => 'Trunk to make call via'), 'via Trunk:');
            echo form::dropdown('number[options][trunk_id]', $trunks);
        ?>
        </div>
    </div>

    <div id="via_uri">
        <div class="field">
        <?php
            echo form::label(array('for' => 'number[options][sipuri]', 'hint' => 'Format is user@domain.com or user@1.2.3.4'), 'SIP URI:');
            echo form::input('number[options][sipuri]');
        ?>
        </div>

        <div class="field">
        <?php
            if (isset($interfaces)) {
                echo form::label(array('for' => 'number[options][interface]', 'hint' => 'Network interface/IP address to use'), 'via Network Interface:');
                echo form::dropdown('number[options][interface]', $interfaces);
            }
        ?>
        </div>
    </div>

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


<?php javascript::codeBlock(); ?>
$('form#destination_selector #number_options_routetype').change(function() {
    if ($('form#destination_selector #number_options_routetype').val() == 1) {
        $('form#destination_selector #via_trunk').show();
        $('form#destination_selector #via_uri').hide();
    } else {
        $('form#destination_selector #via_trunk').hide();
        $('form#destination_selector #via_uri').show();
    }
});
<?php javascript::blockEnd(); ?>
