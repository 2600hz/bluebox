<div id="external_xfer_update_header" class="update external_xfer module_header">
    <h2><span class="helptip"></span><?php echo __($title); ?></h2>
</div>

<div id="external_xfer_add_form" class="txt-left form external_xfer add">
    <?php echo form::open(); ?>

    <?php echo form::open_section('External Transfer Details'); ?>

        <div class="field">
            <?php echo form::label('externalxfer[name]', 'Name:'); ?>
            <?php echo form::input(array('name' => 'externalxfer[name]')); ?>
        </div>

        <div class="field">
            <?php echo form::label('externalxfer[description]', 'Description:'); ?>
            <?php echo form::input(array('name' => 'externalxfer[description]')); ?>
        </div>

    <?php echo form::close_section(); ?>


    <?php echo form::open_section('Destination'); ?>

    <div class="field">
    <?php
        echo form::label('externalxfer[route_type]', 'Routing Method:');
        echo form::dropdown('externalxfer[route_type]', array(1 => 'via Trunk', 2 => 'via SIP URI'));
    ?>
    </div>

    <div id="via_trunk">
        <div class="field">
        <?php
            echo form::label(array('for' => 'externalxfer[route_details][trunk]', 'hint' => 'Trunk to make call via'), 'Trunk:');
            echo form::dropdown('externalxfer[route_details][trunk]', $trunks);
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label(array('for' => 'externalxfer[route_details][number]'), 'Number to Call:');
            echo form::input('externalxfer[route_details][number]');
        ?>
        </div>
    </div>

    <div id="via_uri">
        <div class="field">
        <?php
            if (isset($interfaces)) {
                echo form::label(array('for' => 'externalxfer[route_details][interface]', 'hint' => 'Network interface/IP address to use'), 'Network Interface:');
                echo form::dropdown('externalxfer[route_details][interface]', $interfaces);
            }
        ?>
        </div>
        
        <div class="field">
        <?php
            echo form::label(array('for' => 'externalxfer[route_details][sipuri]', 'hint' => 'Format is user@domain.com or user@1.2.3.4'), 'SIP URI:');
            echo form::input('externalxfer[route_details][sipuri]');
        ?>
        </div>
    </div>

    <?php echo form::close_fieldset(); ?>

    <?php echo form::close_section(); ?>

    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>
    </div>

    <?php echo form::close(); ?>
</div>



<?php javascript::codeBlock(); ?>
$('form.externalxfer #externalxfer_route_type').change(function() {
    if ($('form.externalxfer #externalxfer_route_type').val() == 1) {
        $('form.externalxfer #via_uri').hide();
        $('form.externalxfer #via_trunk').fadeIn();
    } else {
        $('form.externalxfer #via_trunk').hide();
        $('form.externalxfer #via_uri').fadeIn();
    }
}).trigger('change');

$(document).bind('destination_externalxfer_submit', function() {
    if ($('form.externalxfer #externalxfer_route_type').val() == 1) {
        $('form.externalxfer input[name="friendly_name"]').val('External Transfer to ' + $('.destination_externalxfer #externalxfer_number').val() + ' (via trunk ' + $('.destination_externalxfer #externalxfer_trunk option:selected').text() + ')');
    } else {
        $('form.externalxfer input[name="friendly_name"]').val('External Transfer to ' + $('.destination_externalxfer #externalxfer_sipuri').val() + ' (via interface ' + $('.destination_externalxfer #externalxfer_interface option:selected').text() + ')');
    }
});
<?php javascript::blockEnd(); ?>
