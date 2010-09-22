<div id="external_xfer_update_header" class="update external_xfer module_header">

    <h2><?php echo __($title); ?></h2>
    
</div>

<div id="external_xfer_add_form" class="txt-left form external_xfer add">

    <?php echo form::open(); ?>

    <?php echo form::open_section('External Transfer Details'); ?>

        <div class="field">
            <?php echo form::label('externalxfer[name]', 'Name:'); ?>
            <?php echo form::input('externalxfer[name]'); ?>
        </div>

        <div class="field">
            <?php echo form::label('externalxfer[description]', 'Description:'); ?>
            <?php echo form::input('externalxfer[description]'); ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Destination'); ?>

    <div class="field">
    <?php
        echo form::label('externalxfer[route_type]', 'Routing Method:');
        echo form::dropdown('externalxfer[route_type]', $route_types);
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
            echo form::label(array('for' => 'externalxfer[route_details][sipuri]', 'hint' => 'Format is user@domain'), 'SIP URI:');
            echo form::input('externalxfer[route_details][sipuri]');
        ?>
        </div>
    </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Number Assignment'); ?>

    <div class="field">
    <?php
        echo form::label('selectednumber', 'Number:');
        echo form::dropdown('selectednumber', $numbers,$numberdefault);
    ?>

    </div>

    <?php echo form::close_section(); ?>
    

    <div class="buttons form_bottom">

        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>

        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>

    </div>

    <?php echo form::close(); ?>

</div>

<?php javascript::codeBlock(); ?>

    $('#externalxfer_route_type').change(function() {
        if ($('#externalxfer_route_type').val() == 1)
        {
            $('#via_uri').hide();

            $('#via_trunk').fadeIn();
        }
        else
        {
            $('#via_trunk').hide();

            $('#via_uri').fadeIn();
        }
    }).trigger('change');

<?php javascript::blockEnd(); ?>
