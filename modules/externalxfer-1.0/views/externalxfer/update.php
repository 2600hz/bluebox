<div id="external_xfer_update_header" class="update external_xfer module_header">

    <h2><?php echo $title; ?></h2>
    
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

    <?php echo form::open_section('Options'); ?>

        <div class="field">
            <?php echo form::label('externalxfer[registry][ignore_early_media]', 'Ignore Early Media:'); ?>
            <?php echo form::checkbox('externalxfer[registry][ignore_early_media]'); ?>
        </div>

        <div class="field">
            <?php echo form::label('externalxfer[registry][require_confirmation]', 'Confirm on Answer:'); ?>
            <?php echo form::checkbox('externalxfer[registry][require_confirmation]'); ?>
        </div>

    <?php echo form::close_section(); ?>
    
    <?php
        if (isset($views))
        {
            echo subview::renderAsSections($views);
        }
    ?>

    <?php echo form::close(TRUE); ?>

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
